<?php
use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile;
use App\Models\ProfessionalProfile;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

User::whereIn('email', ['aluno@academia.com', 'paciente@academia.com', 'profissional@academia.com'])->delete();

$alunoRole = Role::where('name', 'aluno')->first();
$pacienteRole = Role::where('name', 'paciente')->first();
$profRole = Role::where('name', 'professional')->first();

    $u1 = new User([
        'name' => 'Aluno Teste',
        'email' => 'aluno@academia.com',
        'profile_id' => $alunoRole->id,
        'plan_id' => 1,
        'status' => 'active',
        'registration_approval_status' => 'approved',
        'email_verified_at' => Carbon::now(),
    ]);
    $u1->password_hash = Hash::make('Academia@2026');
    $u1->save();
    $u1->roles()->attach($alunoRole->id);
    UserProfile::create(['user_id' => $u1->id, 'birth_date' => '1990-01-01', 'sex' => 'M']);

    $u2 = new User([
        'name' => 'Paciente Teste',
        'email' => 'paciente@academia.com',
        'profile_id' => $pacienteRole->id ?? $alunoRole->id,
        'plan_id' => 1,
        'status' => 'active',
        'registration_approval_status' => 'approved',
        'email_verified_at' => Carbon::now(),
    ]);
    $u2->password_hash = Hash::make('Academia@2026');
    $u2->save();
    $u2->roles()->attach($pacienteRole->id ?? $alunoRole->id);
    UserProfile::create(['user_id' => $u2->id, 'birth_date' => '1995-01-01', 'sex' => 'F']);

    $u3 = new User([
        'name' => 'Profissional Teste',
        'email' => 'profissional@academia.com',
        'profile_id' => $profRole->id,
        'plan_id' => 1,
        'status' => 'active',
        'registration_approval_status' => 'approved',
        'email_verified_at' => Carbon::now(),
    ]);
    $u3->password_hash = Hash::make('Academia@2026');
    $u3->save();
    $u3->roles()->attach($profRole->id);
    UserProfile::create(['user_id' => $u3->id, 'birth_date' => '1985-01-01', 'sex' => 'M']);
    
    $profession = \App\Models\Profession::first();
    if ($profession) {
        ProfessionalProfile::create([
            'user_id' => $u3->id,
            'profession_id' => $profession->id,
            'registration_number' => 'CRM-123',
            'council' => 'CRM',
            'registration_uf' => 'SP',
            'registration_expiry_date' => '2030-12-31',
            'created_by' => 1,
            'updated_by' => 1
        ]);
    }
echo "Users recreated successfully.\n";
