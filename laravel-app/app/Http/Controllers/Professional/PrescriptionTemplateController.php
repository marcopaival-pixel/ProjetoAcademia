<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\PrescriptionTemplate;
use App\Models\Especialidade;
use Illuminate\Http\Request;

class PrescriptionTemplateController extends Controller
{
    public function index()
    {
        $templates = PrescriptionTemplate::where('professional_id', auth()->id())
            ->with('specialty')
            ->get();

        return view('professional.templates.index', compact('templates'));
    }

    public function create()
    {
        $specialties = Especialidade::active()->get();
        return view('professional.templates.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'especialidade_id' => 'required|exists:especialidades,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        PrescriptionTemplate::create(array_merge($validated, [
            'professional_id' => auth()->id(),
        ]));

        return redirect()->route('professional.templates.index')->with('success', 'Template salvo com sucesso.');
    }

    public function edit(PrescriptionTemplate $template)
    {
        $this->authorizeAccess($template);
        $specialties = Especialidade::active()->get();
        return view('professional.templates.edit', compact('template', 'specialties'));
    }

    public function update(Request $request, PrescriptionTemplate $template)
    {
        $this->authorizeAccess($template);

        $validated = $request->validate([
            'especialidade_id' => 'required|exists:especialidades,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->update($validated);

        return redirect()->route('professional.templates.index')->with('success', 'Template atualizado.');
    }

    public function destroy(PrescriptionTemplate $template)
    {
        $this->authorizeAccess($template);
        $template->delete();

        return redirect()->route('professional.templates.index')->with('success', 'Template removido.');
    }

    private function authorizeAccess(PrescriptionTemplate $template)
    {
        if ($template->professional_id !== auth()->id()) {
            abort(403);
        }
    }
}
