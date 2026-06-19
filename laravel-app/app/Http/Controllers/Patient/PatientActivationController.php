<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\PatientAccessToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientActivationController extends Controller
{
    /**
     * Exibe o formulário de ativação de conta.
     */
    public function show($token)
    {
        $tokenHash = hash('sha256', $token);
        $accessToken = PatientAccessToken::where('token_hash', $tokenHash)
            ->where('type', 'activation')
            ->where('status', 'active')
            ->first();

        if (!$accessToken || !$accessToken->isValid()) {
            return view('patient.activation_error', [
                'error' => 'Token inválido ou expirado. Solicite um novo link ao seu profissional.'
            ]);
        }

        $patient = $accessToken->patient;

        if ($patient->status === 'active') {
             return view('patient.activation_error', [
                'error' => 'Esta conta já foi ativada. Você pode realizar o login normalmente.'
            ]);
        }

        return view('patient.activation', compact('patient', 'token'));
    }

    /**
     * Processa a ativação da conta.
     */
    public function activate(Request $request, $token)
    {
        $tokenHash = hash('sha256', $token);
        $accessToken = PatientAccessToken::where('token_hash', $tokenHash)
            ->where('type', 'activation')
            ->where('status', 'active')
            ->first();

        if (!$accessToken || !$accessToken->isValid()) {
            return back()->withErrors(['token' => 'Token inválido ou expirado.']);
        }

        $patient = $accessToken->patient;

        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => ['required', 'string', new \App\Rules\CpfValido()],
            'birth_date' => 'required|date|before:today',
            'sex' => 'required|in:M,F',
            'height_cm' => 'required|integer|min:50|max:250',
            'weight_kg' => 'required|numeric|min:20|max:500',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'has_disease' => 'required|boolean',
            'disease_details' => 'required_if:has_disease,1|nullable|string',
            'has_injury' => 'required|boolean',
            'injury_details' => 'required_if:has_injury,1|nullable|string',
            'uses_medication' => 'required|boolean',
            'medication_details' => 'required_if:uses_medication,1|nullable|string',
            'has_allergy' => 'required|boolean',
            'allergy_details' => 'required_if:has_allergy,1|nullable|string',
            'activity_level' => 'required|string',
            'goal' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
            'truth_confirmation' => 'accepted',
        ]);

        // Verificação de segurança: CPF deve bater com o cadastrado (normalizado)
        $inputCpf = \App\Support\Cpf::normalize($request->cpf);
        if ($inputCpf !== $patient->cpf) {
            return back()->withErrors(['cpf' => 'O CPF informado não corresponde aos dados cadastrados.'])->withInput();
        }

        // Atualiza dados do usuário
        $patient->update([
            'name' => $request->name,
            'status' => 'active',
            'activated_at' => now(),
            'perfil_paciente_completo' => true,
        ]);

        $patient->password_hash = Hash::make($request->password);
        $patient->save();

        // Atualiza perfil completo
        $patient->profile()->updateOrCreate(
            ['user_id' => $patient->id],
            [
                'birth_date' => $request->birth_date,
                'sex' => $request->sex,
                'height_cm' => $request->height_cm,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'has_disease' => $request->has_disease,
                'disease_details' => $request->disease_details,
                'has_injury' => $request->has_injury,
                'injury_details' => $request->injury_details,
                'uses_medication' => $request->uses_medication,
                'medication_details' => $request->medication_details,
                'has_allergy' => $request->has_allergy,
                'allergy_details' => $request->allergy_details,
                'activity_level' => $request->activity_level,
                'goal' => $request->goal,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'profile_completed_at' => now(),
            ]
        );

        // Registro de peso inicial (Step 4: Peso)
        $patient->weightEntries()->create([
            'weight_kg' => $request->weight_kg,
            'weighed_at' => now(),
        ]);

        // Inativa o token
        $accessToken->update([
            'status' => 'used',
            'used_at' => now()
        ]);

        // Logout para forçar novo acesso após conclusão
        Auth::logout();

        return redirect()->route('login')
            ->with('success', 'Cadastro concluído com sucesso! Agora você pode realizar o login.');
    }

    /**
     * Exibe o formulário de complementação de dados para usuários logados.
     */
    public function completeProfileShow()
    {
        $patient = auth()->user();
        if ($patient->perfil_paciente_completo) {
            return redirect()->route('patient.portal');
        }

        $token = 'logged-in';
        return view('patient.activation', compact('patient', 'token'));
    }

    /**
     * Processa a complementação de dados para usuários logados.
     */
    public function completeProfileStore(Request $request)
    {
        $patient = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'cpf' => ['required', 'string', new \App\Rules\CpfValido()],
            'birth_date' => 'required|date|before:today',
            'sex' => 'required|in:M,F',
            'height_cm' => 'required|integer|min:50|max:250',
            'weight_kg' => 'required|numeric|min:20|max:500',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|size:2',
            'has_disease' => 'required|boolean',
            'disease_details' => 'required_if:has_disease,1|nullable|string',
            'has_injury' => 'required|boolean',
            'injury_details' => 'required_if:has_injury,1|nullable|string',
            'uses_medication' => 'required|boolean',
            'medication_details' => 'required_if:uses_medication,1|nullable|string',
            'has_allergy' => 'required|boolean',
            'allergy_details' => 'required_if:has_allergy,1|nullable|string',
            'activity_level' => 'required|string',
            'goal' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'truth_confirmation' => 'accepted',
            'terms' => 'accepted',
        ];

        // Se o usuário não tiver senha definida (ex: vindo de login social) ou estiver pendente, exige senha
        if (!$patient->password_hash || $patient->status === 'pending') {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules);

        // Verificação de segurança: CPF deve bater se já estiver cadastrado
        $inputCpf = \App\Support\Cpf::normalize($request->cpf);
        if ($patient->cpf && $inputCpf !== $patient->cpf) {
            return back()->withErrors(['cpf' => 'O CPF informado não corresponde aos dados cadastrados.'])->withInput();
        }

        // Atualiza dados do usuário
        $updateData = [
            'name' => $request->name,
            'perfil_paciente_completo' => true,
        ];

        if ($patient->status === 'pending') {
            $updateData['status'] = 'active';
            $updateData['activated_at'] = now();
        }

        
        if (!$patient->cpf) {
            $updateData['cpf'] = $inputCpf;
        }

        if ($request->filled('password')) {
            $patient->password_hash = Hash::make($request->password);
        }

        $patient->update($updateData);

        // Atualiza perfil completo
        $patient->profile()->updateOrCreate(
            ['user_id' => $patient->id],
            [
                'birth_date' => $request->birth_date,
                'sex' => $request->sex,
                'height_cm' => $request->height_cm,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'has_disease' => $request->has_disease,
                'disease_details' => $request->disease_details,
                'has_injury' => $request->has_injury,
                'injury_details' => $request->injury_details,
                'uses_medication' => $request->uses_medication,
                'medication_details' => $request->medication_details,
                'has_allergy' => $request->has_allergy,
                'allergy_details' => $request->allergy_details,
                'activity_level' => $request->activity_level,
                'goal' => $request->goal,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'profile_completed_at' => now(),
            ]
        );

        // Registro de peso inicial
        $patient->weightEntries()->create([
            'weight_kg' => $request->weight_kg,
            'weighed_at' => now(),
        ]);

        // Logout para forçar novo acesso após conclusão
        Auth::logout();

        return redirect()->route('login')
            ->with('success', 'Perfil completado com sucesso! Realize o login para acessar o portal.');
    }
}
