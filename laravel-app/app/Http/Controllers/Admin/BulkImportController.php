<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BulkImportService;
use App\Models\AdminLog;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkImportController extends Controller
{
    protected $importService;

    public function __construct(BulkImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Download CSV template for a specific module.
     */
    public function downloadTemplate(string $module): StreamedResponse
    {
        $templates = [
            'pacientes' => [
                'headers' => ['nome', 'cpf', 'email', 'telefone', 'data_nascimento', 'sexo', 'altura', 'peso', 'endereco', 'cidade', 'estado', 'status', 'observacoes', 'profissional_responsavel'],
                'example' => ['João Silva', '123.456.789-00', 'joao@email.com', '11988887777', '15/05/1990', 'M', '175', '80.5', 'Rua A, 100', 'São Paulo', 'SP', 'ativo', 'Sem alergias', 'pro@email.com']
            ],
            'profissionais' => [
                'headers' => ['nome', 'cpf', 'email', 'telefone', 'especialidade', 'registro_profissional', 'status', 'foto', 'endereco', 'cidade', 'estado'],
                'example' => ['Dr. Carlos', '987.654.321-99', 'carlos@pro.com', '11977776666', 'Personal Trainer', 'CREF 123456-G/SP', 'ativo', '', 'Av B, 200', 'São Paulo', 'SP']
            ],
            'alunos' => [
                'headers' => ['nome', 'cpf', 'email', 'telefone', 'data_nascimento', 'sexo', 'altura', 'peso', 'objetivo', 'status', 'observacoes', 'plano'],
                'example' => ['Maria Souza', '444.555.666-77', 'maria@aluno.com', '11966665555', '20/10/2000', 'F', '160', '55.0', 'Emagrecimento', 'ativo', 'Iniciante', 'Plano Black']
            ],
            'treinos' => [
                'headers' => ['nome_treino', 'descricao', 'nivel', 'tipo_treino', 'duracao_minutos', 'status', 'objetivo', 'equipamento', 'observacoes'],
                'example' => ['Treino Iniciante', 'Foco em adaptação', 'Iniciante', 'Full Body', '45', 'ativo', 'Condicionamento', 'Halteres', '3x por semana']
            ],
        ];

        if (!isset($templates[$module])) abort(404);

        $template = $templates[$module];

        return response()->streamDownload(function () use ($template) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $template['headers'], ';');
            fputcsv($handle, $template['example'], ';');
            fclose($handle);
        }, "modelo_{$module}.csv");
    }

    /**
     * Import data for a specific module.
     */
    public function import(Request $request, string $module): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $results = $this->importService->import($module, $file->getRealPath());

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Realizou importação em massa no módulo: " . ucfirst($module),
            'ip_address' => $request->ip(),
            'payload' => [
                'module' => $module,
                'success_count' => $results['success_count'],
                'error_count' => $results['error_count']
            ]
        ]);

        $msg = "Importação concluída. Sucesso: {$results['success_count']}, Erros: {$results['error_count']}.";
        
        if ($results['error_count'] > 0) {
            return back()->with('warning', $msg)->withErrors($results['errors']);
        }

        return back()->with('success', $msg);
    }
}
