<?php

namespace App\Http\Controllers\Admin\ConfigurationCenter;

use App\Http\Controllers\Controller;
use App\Models\AdminEntity;
use App\Services\ConfigurationCenter\EntityService;
use Illuminate\Http\Request;

class EntityController extends Controller
{
    protected $entityService;

    public function __construct(EntityService $entityService)
    {
        $this->entityService = $entityService;
    }

    public function index()
    {
        $entities = AdminEntity::orderBy('sort_order')->get();
        return view('admin.configuration-center.entities.index', compact('entities'));
    }

    public function discovery()
    {
        $tables = $this->entityService->getUnregisteredTables();
        return view('admin.configuration-center.entities.discovery', compact('tables'));
    }

    public function autoRegister(Request $request)
    {
        $request->validate(['table' => 'required|string']);
        
        $entity = $this->entityService->autoRegisterTable($request->table);

        return redirect()->route('admin.configuration-center.entities.edit', $entity->id)
            ->with('success', "Tabela {$request->table} registrada automaticamente! Ajuste as configurações abaixo.");
    }

    public function create()
    {
        return view('admin.configuration-center.entities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:admin_entities,name',
            'display_name' => 'required|string',
            'table_name' => 'required|string',
            'model_class' => 'required|string',
            'icon' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $this->entityService->createEntity($validated);

        return redirect()->route('admin.configuration-center.entities.index')
            ->with('success', 'Entidade criada com sucesso!');
    }

    public function edit(AdminEntity $entity)
    {
        $entity->load('fields');
        return view('admin.configuration-center.entities.edit', compact('entity'));
    }

    public function update(Request $request, AdminEntity $entity)
    {
        $validated = $request->validate([
            'display_name' => 'required|string',
            'icon' => 'nullable|string',
            'category' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $this->entityService->updateEntity($entity, $validated);

        return redirect()->route('admin.configuration-center.entities.index')
            ->with('success', 'Entidade atualizada com sucesso!');
    }

    public function destroy(AdminEntity $entity)
    {
        $entity->delete();
        return redirect()->route('admin.configuration-center.entities.index')
            ->with('success', 'Entidade removida com sucesso!');
    }
}
