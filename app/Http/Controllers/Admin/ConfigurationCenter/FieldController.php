<?php

namespace App\Http\Controllers\Admin\ConfigurationCenter;

use App\Http\Controllers\Controller;
use App\Models\AdminEntity;
use App\Models\AdminField;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(AdminEntity $entity)
    {
        $fields = $entity->fields;
        return view('admin.configuration-center.fields.index', compact('entity', 'fields'));
    }

    public function store(Request $request, AdminEntity $entity)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'label' => 'required|string',
            'type' => 'required|string',
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $entity->fields()->create($validated);

        return redirect()->back()->with('success', 'Campo adicionado com sucesso!');
    }

    public function update(Request $request, AdminField $field)
    {
        $validated = $request->validate([
            'label' => 'required|string',
            'type' => 'required|string',
            'is_required' => 'boolean',
            'is_visible_list' => 'boolean',
            'is_visible_form' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $field->update($validated);

        return redirect()->back()->with('success', 'Campo atualizado com sucesso!');
    }

    public function destroy(AdminField $field)
    {
        $field->delete();
        return redirect()->back()->with('success', 'Campo removido com sucesso!');
    }
}
