<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfessionalProfile;
use App\Models\AcademyCompany;
use App\Models\AcademyUnit;
use App\Models\Role;
use App\Models\Profession;
use App\Models\Especialidade;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isProfessionalUnico = !$user->academy_company_id && $user->hasRole('professional');
        $isClinic = !!$user->academy_company_id;
        $isAdmin = $user->isAdministrator();

        return view('admin.registrations.index', compact('isProfessionalUnico', 'isClinic', 'isAdmin'));
    }

    public function createProfessionalUnico()
    {
        $professions = Profession::all();
        $plans = Plan::where('type', 'professional')->get();
        return view('admin.registrations.professional-unico', compact('professions', 'plans'));
    }

    public function storeProfessionalUnico(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'password' => 'required|min:8',
            'profession_id' => 'required|exists:professions,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'whatsapp' => $request->whatsapp,
                'password_hash' => Hash::make($request->password),
                'user_type' => 'PROFISSIONAL_UNICO',
                'status' => $request->status ?? 'active',
                'academy_company_id' => null,
            ]);

            $user->assignRole('professional');

            ProfessionalProfile::create([
                'user_id' => $user->id,
                'profession_id' => $request->profession_id,
                'registration_number' => $request->registration_number,
                'specialty' => $request->specialty,
                'education' => $request->education,
                'certifications' => $request->certifications,
                'service_types' => [$request->service_types],
                'work_start_time' => $request->work_start_time,
                'work_end_time' => $request->work_end_time,
                'appointment_duration' => $request->appointment_duration,
                'work_days' => $request->work_days,
            ]);

            DB::commit();

            return redirect()->route('admin.registrations.index')->with('success', 'Profissional Único cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar profissional: ' . $e->getMessage())->withInput();
        }
    }

    public function createProfessionalClinica()
    {
        $professions = Profession::all();
        $companies = AcademyCompany::all();
        $user = auth()->user();
        $myCompany = $user->academy_company_id ? AcademyCompany::find($user->academy_company_id) : null;
        
        return view('admin.registrations.professional-clinica', compact('professions', 'companies', 'myCompany'));
    }

    public function storeProfessionalClinica(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'academy_company_id' => 'required|exists:academy_companies,id',
            'profession_id' => 'required|exists:professions,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'whatsapp' => $request->whatsapp,
                'password_hash' => Hash::make($request->password ?? '12345678'), // Default password
                'user_type' => 'PROFISSIONAL_CLINICA',
                'status' => 'active',
                'academy_company_id' => $request->academy_company_id,
                'clinic_role' => $request->clinic_role,
                'link_type' => $request->link_type,
            ]);

            $user->assignRole('professional');

            ProfessionalProfile::create([
                'user_id' => $user->id,
                'profession_id' => $request->profession_id,
                'registration_number' => $request->registration_number,
                'academy_unit_id' => $request->academy_unit_id,
                'room' => $request->room,
                'appointment_duration' => $request->appointment_duration,
                'internal_permissions' => $request->internal_permissions,
            ]);

            DB::commit();

            return redirect()->route('admin.registrations.index')->with('success', 'Profissional da Clínica cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar profissional: ' . $e->getMessage())->withInput();
        }
    }

    public function createFuncionarioClinica()
    {
        $companies = AcademyCompany::all();
        $user = auth()->user();
        $myCompany = $user->academy_company_id ? AcademyCompany::find($user->academy_company_id) : null;
        
        return view('admin.registrations.funcionario-clinica', compact('companies', 'myCompany'));
    }

    public function storeFuncionarioClinica(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'academy_company_id' => 'required|exists:academy_companies,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'whatsapp' => $request->whatsapp,
                'password_hash' => Hash::make($request->password ?? '12345678'),
                'user_type' => 'FUNCIONARIO_CLINICA',
                'status' => 'active',
                'academy_company_id' => $request->academy_company_id,
                'clinic_role' => $request->clinic_role,
                'sector' => $request->sector,
                'link_type' => $request->link_type,
                'admission_date' => $request->admission_date,
            ]);

            // Assign a staff role if exists, otherwise receptionist
            $user->assignRole('receptionist');

            DB::commit();

            return redirect()->route('admin.registrations.index')->with('success', 'Funcionário cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar funcionário: ' . $e->getMessage())->withInput();
        }
    }

    public function createPacienteProfissional()
    {
        return view('admin.registrations.paciente-profissional');
    }

    public function storePacienteProfissional(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'professional_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'whatsapp' => $request->whatsapp,
                'password_hash' => Hash::make(str_replace(['.', '-'], '', $request->cpf)), // CPF as initial password
                'user_type' => 'PACIENTE_PROFISSIONAL',
                'status' => 'active',
                'academy_company_id' => null,
            ]);

            $user->assignRole('paciente');

            // Link to professional
            $user->professionals()->attach($request->professional_id, [
                'patient_type' => 'PACIENTE_PROFISSIONAL',
                'data_cadastro' => now(),
                'status' => 'Sim',
            ]);

            // Health Profile
            $user->profile()->create([
                'birth_date' => $request->birth_date,
                'sex' => $request->sex,
                'height_cm' => $request->height_cm,
                'allergy_details' => $request->allergy_details,
                'medication_details' => $request->medication_details,
            ]);

            DB::commit();

            return redirect()->route('admin.registrations.index')->with('success', 'Paciente cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar paciente: ' . $e->getMessage())->withInput();
        }
    }

    public function createPacienteClinica()
    {
        $companies = AcademyCompany::all();
        $user = auth()->user();
        $myCompany = $user->academy_company_id ? AcademyCompany::find($user->academy_company_id) : null;
        
        return view('admin.registrations.paciente-clinica', compact('companies', 'myCompany'));
    }

    public function storePacienteClinica(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|unique:users,cpf',
            'academy_company_id' => 'required|exists:academy_companies,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'cpf' => $request->cpf,
                'whatsapp' => $request->whatsapp,
                'password_hash' => Hash::make(str_replace(['.', '-'], '', $request->cpf)),
                'user_type' => 'PACIENTE_CLINICA',
                'status' => 'active',
                'academy_company_id' => $request->academy_company_id,
            ]);

            $user->assignRole('paciente');

            // Base linkage to clinic (optional: link to first professional or stay as clinic-only)
            // No professional link here yet, user can link later.

            // Health Profile
            $user->profile()->create([
                'birth_date' => $request->birth_date,
                'sex' => $request->sex,
                'allergy_details' => $request->allergy_details,
            ]);

            DB::commit();

            return redirect()->route('admin.registrations.paciente.vincular', $user->id)
                ->with('success', 'Paciente cadastrado! Agora vincule os profissionais de atendimento.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar paciente: ' . $e->getMessage())->withInput();
        }
    }

    public function vincularProfissional(User $user)
    {
        $companyId = $user->academy_company_id;
        $professionals = User::whereHas('roles', fn($q) => $q->where('name', 'professional'))
            ->when($companyId, fn($q) => $q->where('academy_company_id', $companyId))
            ->get();
            
        $linkedProfessionals = $user->professionals;

        return view('admin.registrations.vincular-paciente', compact('user', 'professionals', 'linkedProfessionals'));
    }

    public function storeVinculo(Request $request, User $user)
    {
        $request->validate([
            'professional_id' => 'required|exists:users,id',
        ]);

        $user->professionals()->syncWithoutDetaching([$request->professional_id => [
            'data_cadastro' => now(),
            'status' => 'Sim',
            'empresa_id' => $user->academy_company_id,
        ]]);

        return back()->with('success', 'Profissional vinculado com sucesso!');
    }

    public function removeVinculo(User $user, User $professional)
    {
        $user->professionals()->detach($professional->id);
        return back()->with('success', 'Vínculo removido com sucesso!');
    }
}
