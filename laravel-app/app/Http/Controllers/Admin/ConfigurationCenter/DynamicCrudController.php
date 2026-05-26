<?php

namespace App\Http\Controllers\Admin\ConfigurationCenter;

use App\Http\Controllers\Controller;
use App\Models\AdminEntity;
use App\Services\ConfigurationCenter\DynamicCrudService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DynamicCrudController extends Controller
{
    protected $crudService;

    public function __construct(DynamicCrudService $crudService)
    {
        $this->crudService = $crudService;
    }

    public function index(string $entityName, Request $request)
    {
        $entity = AdminEntity::where('name', $entityName)->where('is_active', true)->firstOrFail();
        $fields = $entity->fields()->where('is_visible_list', true)->get();
        
        $filters = $request->only(['search', 'sort_by', 'sort_dir', 'trashed']);
        foreach ($entity->fields()->where('is_filterable', true)->get() as $f) {
            if ($request->has($f->name)) {
                $filters[$f->name] = $request->get($f->name);
            }
        }

        $records = $this->crudService->getList($entity, $filters);

        return view('admin.configuration-center.crud.index', compact('entity', 'fields', 'records'));
    }

    public function create(string $entityName)
    {
        $entity = AdminEntity::where('name', $entityName)->firstOrFail();
        $fields = $entity->fields()->where('is_visible_form', true)->get();

        return view('admin.configuration-center.crud.create', compact('entity', 'fields'));
    }

    public function store(string $entityName, Request $request)
    {
        $entity = AdminEntity::where('name', $entityName)->firstOrFail();
        $fields = $entity->fields()->where('is_visible_form', true)->get();

        $rules = [];
        foreach ($fields as $field) {
            if ($field->validation_rules) {
                $rules[$field->name] = $field->validation_rules;
            } elseif ($field->is_required) {
                $rules[$field->name] = 'required';
            }
        }

        $validated = $request->validate($rules);

        $this->crudService->store($entity, $validated);

        return redirect()->route('admin.configuration-center.crud.index', $entityName)
            ->with('success', 'Registro criado com sucesso!');
    }

    public function edit(string $entityName, $id)
    {
        $entity = AdminEntity::where('name', $entityName)->firstOrFail();
        $fields = $entity->fields()->where('is_visible_form', true)->get();
        
        $modelClass = $entity->model_class;
        $record = $modelClass::findOrFail($id);

        return view('admin.configuration-center.crud.edit', compact('entity', 'fields', 'record'));
    }

    public function update(string $entityName, $id, Request $request)
    {
        $entity = AdminEntity::where('name', $entityName)->firstOrFail();
        $fields = $entity->fields()->where('is_visible_form', true)->get();

        $rules = [];
        foreach ($fields as $field) {
            if ($field->validation_rules) {
                $rules[$field->name] = $field->validation_rules;
            } elseif ($field->is_required) {
                $rules[$field->name] = 'required';
            }
        }

        $validated = $request->validate($rules);

        $this->crudService->update($entity, $id, $validated);

        return redirect()->route('admin.configuration-center.crud.index', $entityName)
            ->with('success', 'Registro atualizado com sucesso!');
    }

    public function destroy(string $entityName, $id)
    {
        $entity = AdminEntity::where('name', $entityName)->firstOrFail();
        $this->crudService->delete($entity, $id);

        return redirect()->route('admin.configuration-center.crud.index', $entityName)
            ->with('success', 'Registro removido com sucesso!');
    }

    public function restore(string $entityName, $id)
    {
        $entity = AdminEntity::where('name', $entityName)->firstOrFail();
        $this->crudService->restore($entity, $id);

        return redirect()->route('admin.configuration-center.crud.index', $entityName)
            ->with('success', 'Registro restaurado com sucesso!');
    }
}
