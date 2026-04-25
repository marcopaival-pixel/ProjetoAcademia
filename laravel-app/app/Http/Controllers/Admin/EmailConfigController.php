<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminTestEmail;
use App\Models\AcademyCompany;
use App\Models\ConfiguracaoEmail;
use App\Services\TransactionalMailService;
use App\Support\MailSendType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailConfigController extends Controller
{
    public function index(): View
    {
        $companies = AcademyCompany::query()
            ->with('configuracaoEmail')
            ->orderBy('name')
            ->get();

        return view('admin.email.providers-index', compact('companies'));
    }

    public function edit(AcademyCompany $academyCompany): View
    {
        $config = ConfiguracaoEmail::firstOrNew(['empresa_id' => $academyCompany->id]);

        return view('admin.email.providers-edit', [
            'company' => $academyCompany,
            'config' => $config,
        ]);
    }

    public function update(Request $request, AcademyCompany $academyCompany): RedirectResponse
    {
        $data = $request->validate([
            'nome_provedor' => 'required|string|max:120',
            'tipo_envio' => 'required|in:smtp,api',
            'preset' => 'required|string|max:32',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_porta' => 'nullable|integer|min:1|max:65535',
            'smtp_usuario' => 'nullable|string|max:255',
            'smtp_senha' => 'nullable|string|max:500',
            'criptografia' => 'required|in:tls,ssl,none',
            'email_remetente' => 'nullable|email|max:255',
            'nome_remetente' => 'nullable|string|max:255',
            'timeout' => 'nullable|integer|min:5|max:600',
            'limite_envio_por_hora' => 'nullable|integer|min:1|max:100000',
            'ativo' => 'boolean',
        ]);

        $config = ConfiguracaoEmail::firstOrNew(['empresa_id' => $academyCompany->id]);
        $config->fill([
            'nome_provedor' => $data['nome_provedor'],
            'tipo_envio' => $data['tipo_envio'],
            'preset' => $data['preset'],
            'smtp_host' => $data['smtp_host'] ?? null,
            'smtp_porta' => $data['smtp_porta'] ?? 587,
            'smtp_usuario' => $data['smtp_usuario'] ?? null,
            'criptografia' => $data['criptografia'],
            'email_remetente' => $data['email_remetente'] ?? null,
            'nome_remetente' => $data['nome_remetente'] ?? null,
            'timeout' => $data['timeout'] ?? 30,
            'limite_envio_por_hora' => $data['limite_envio_por_hora'] ?? 100,
            'ativo' => $request->boolean('ativo'),
        ]);

        if (! empty($data['smtp_senha'])) {
            $config->smtp_senha = $data['smtp_senha'];
        }

        $config->empresa_id = $academyCompany->id;
        $config->save();

        return redirect()->route('admin.settings.email.providers.edit', $academyCompany)
            ->with('success', 'Configuração de e-mail guardada.');
    }

    public function test(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'empresa_id' => 'required|exists:academy_companies,id',
            'email_destino' => 'required|email',
        ]);

        $company = AcademyCompany::findOrFail($data['empresa_id']);
        $admin = auth()->user();

        $ok = app(TransactionalMailService::class)->send(
            new AdminTestEmail($admin->name),
            $data['email_destino'],
            (int) $company->id,
            $admin->id,
            'Teste de e-mail — '.$company->name,
            'Teste de configuração por empresa',
            MailSendType::TEST
        );

        if ($ok) {
            return back()->with('success', 'E-mail enviado com sucesso.');
        }

        return back()->with('error', 'Falha ao enviar e-mail.');
    }

    public function deactivate(AcademyCompany $academyCompany): RedirectResponse
    {
        ConfiguracaoEmail::where('empresa_id', $academyCompany->id)->update(['ativo' => false]);

        return back()->with('success', 'Envio de e-mail desativado para esta empresa.');
    }
}
