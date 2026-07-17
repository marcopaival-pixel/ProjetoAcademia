<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::all()->groupBy(function($p) {
            return explode('.', $p->name)[0]; // Agrupar por prefixo
        });
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name|max:50',
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create($request->only(['name', 'label', 'description']));
        
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Criou o perfil: {$role->label}",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Perfil criado com sucesso.');
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::all()->groupBy(function($p) {
            return explode('.', $p->name)[0];
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($request->only(['name', 'label', 'description']));
        
        $role->permissions()->sync($request->permissions ?? []);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Editou o perfil: {$role->label}",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return back()->with('error', 'Não é possível excluir um perfil que possui usuários vinculados.');
        }

        $label = $role->label;
        $role->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu o perfil: {$label}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Perfil removido com sucesso.');
    }
}
