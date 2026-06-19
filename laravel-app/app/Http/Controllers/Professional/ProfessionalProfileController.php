<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfessionalProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $profile = $user->professionalProfile ?? $user->professionalProfile()->create([
            'registration_number' => 'PENDENTE',
            'council' => 'PENDENTE',
            'registration_uf' => 'SP',
            'registration_expiry_date' => now()->addYear(),
            'profession_id' => 1, // Default or find first
        ]);
        $professions = \App\Models\Profession::all();
        $especialidades = \App\Models\Especialidade::all();

        return view('professional.profile.edit', compact('user', 'profile', 'professions', 'especialidades'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $profile = $user->professionalProfile;

        $messages = [
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'O formato do e-mail é inválido.',
            'unique' => 'Este e-mail já está em uso.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'min' => 'O campo :attribute deve ter pelo menos :min.',
            'date_format' => 'O horário deve estar no formato HH:mm.',
            'image' => 'O arquivo deve ser uma imagem.',
        ];

        $attributes = [
            'name' => 'Nome',
            'email' => 'E-mail',
            'registration_number' => 'Nº de Registro',
            'council' => 'Conselho',
            'registration_uf' => 'UF do Registro',
            'experience_years' => 'Anos de Experiência',
            'work_start_time' => 'Horário Inicial',
            'work_end_time' => 'Horário Final',
        ];

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            
            // Dados Profissionais
            'profession_id' => 'required|exists:professions,id',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'registration_number' => 'required|string',
            'council' => 'required|string',
            'registration_uf' => 'required|string|size:2',
            
            // Perfil Público
            'about' => 'nullable|string',
            'offered_services' => 'nullable|string',
            'specialty' => 'nullable|string',
            'professional_photo' => 'nullable|image|max:2048',
            
            // Atendimento
            'service_types' => 'nullable|array',
            'consultation_price' => 'nullable|numeric|min:0',
            'appointment_duration' => 'nullable|integer|min:1',
            'appointment_interval' => 'nullable|integer|min:0',
            
            // Local
            'company_name' => 'nullable|string|max:255',
            'clinic_address' => 'nullable|string|max:255',
            'clinic_city' => 'nullable|string|max:100',
            'clinic_state' => 'nullable|string|size:2',
            
            // Agenda
            'work_days' => 'nullable|array',
            'work_start_time' => 'nullable|date_format:H:i',
            'work_end_time' => 'nullable|date_format:H:i',
            
            // Visibilidade e Módulos
            'is_public' => 'nullable|boolean',
            'use_finance_module' => 'nullable|boolean',
        ], $messages, $attributes);

        // Update User
        $user->update([
            'name' => $data['name'],
            'cpf' => $data['cpf'],
            'phone' => $data['phone'],
            'email' => $data['email'],
        ]);

        // Handle Photo
        if ($request->hasFile('professional_photo')) {
            if ($profile->professional_photo_path) {
                Storage::delete($profile->professional_photo_path);
            }
            $path = $request->file('professional_photo')->store('professionals/photos', 'public');
            $data['professional_photo_path'] = $path;
        }

        // Prepare data for profile update
        $profileData = $request->only([
            'profession_id', 'experience_years', 'education', 'registration_number', 'council', 
            'registration_uf', 'about', 'offered_services', 'specialty',
            'service_types', 'consultation_price', 'appointment_duration', 
            'appointment_interval', 'company_name', 'clinic_address', 
            'clinic_city', 'clinic_state', 'work_days', 'work_start_time', 
            'work_end_time'
        ]);

        $profileData['is_public'] = $request->boolean('is_public');
        $profileData['use_finance_module'] = $request->boolean('use_finance_module');
        
        if (isset($data['professional_photo_path'])) {
            $profileData['professional_photo_path'] = $data['professional_photo_path'];
        }

        $profile->update($profileData);

        // Sync Agenda (ProfessionalAvailability table)
        if (isset($profileData['work_days']) && $profileData['work_start_time'] && $profileData['work_end_time']) {
            $dayMap = [
                'Domingo' => 0,
                'Segunda' => 1,
                'Terça' => 2,
                'Quarta' => 3,
                'Quinta' => 4,
                'Sexta' => 5,
                'Sábado' => 6,
            ];

            \App\Models\ProfessionalAvailability::where('professional_id', $user->id)->delete();
            
            foreach ($profileData['work_days'] as $dayName) {
                if (isset($dayMap[$dayName])) {
                    \App\Models\ProfessionalAvailability::create([
                        'professional_id' => $user->id,
                        'day_of_week' => $dayMap[$dayName],
                        'start_time' => $profileData['work_start_time'],
                        'end_time' => $profileData['work_end_time'],
                    ]);
                }
            }
        }

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}


