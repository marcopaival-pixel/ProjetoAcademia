<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyCompany;
use App\Models\EmailTemplate;
use App\Support\EmailTemplateType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function index(): View
    {
        $templates = EmailTemplate::query()
            ->with('empresa')
            ->orderByDesc('ativo')
            ->orderBy('tipo')
            ->paginate(30);

        return view('admin.email.templates-index', compact('templates'));
    }

    public function create(): View
    {
        $companies = AcademyCompany::orderBy('name')->get();
        $tipos = EmailTemplateType::labels();

        return view('admin.email.templates-form', [
            'template' => new EmailTemplate(['ativo' => true]),
            'companies' => $companies,
            'tipos' => $tipos,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        EmailTemplate::create($data);

        return redirect()->route('admin.settings.email.templates.index')
            ->with('success', 'Template criado.');
    }

    public function edit(EmailTemplate $emailTemplate): View
    {
        $companies = AcademyCompany::orderBy('name')->get();
        $tipos = EmailTemplateType::labels();

        return view('admin.email.templates-form', [
            'template' => $emailTemplate,
            'companies' => $companies,
            'tipos' => $tipos,
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $emailTemplate->update($this->validated($request));

        return redirect()->route('admin.settings.email.templates.index')
            ->with('success', 'Template atualizado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'empresa_id' => 'nullable|exists:academy_companies,id',
            'tipo' => ['required', 'string', 'max:64', Rule::in(array_keys(EmailTemplateType::labels()))],
            'nome_template' => 'required|string|max:160',
            'assunto' => 'required|string|max:500',
            'mensagem' => 'required|string',
            'variaveis' => 'nullable|string',
            'ativo' => 'boolean',
        ]);
        $data['empresa_id'] = $data['empresa_id'] ?? null;
        $data['ativo'] = $request->boolean('ativo');

        return $data;
    }
}
