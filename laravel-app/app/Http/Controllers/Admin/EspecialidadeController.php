<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidade;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EspecialidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Especialidade::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
        }

        $especialidades = $query->with('profession')->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        $professions = \App\Models\Profession::all();

        return view('admin.especialidades.index', compact('especialidades', 'professions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nome' => 'required|string|max:120|unique:especialidades,nome',
            'codigo' => 'required|string|max:50|unique:especialidades,codigo',
            'categoria' => 'required|string|max:64',
            'icone' => 'nullable|string|max:64',
            'status' => 'required|in:Ativo,Inativo',
            'profession_id' => 'nullable|exists:professions,id',
        ], [
            'nome.unique' => 'Já existe uma especialidade com este nome.',
            'codigo.unique' => 'Já existe uma especialidade com este código.',
        ]);

        $especialidade = Especialidade::create($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Cadastrou a especialidade: {$especialidade->nome} (#{$especialidade->id})",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.especialidades.index')->with('success', 'Especialidade cadastrada com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Especialidade $especialidade): RedirectResponse
    {
        $data = $request->validate([
            'nome' => 'required|string|max:120|unique:especialidades,nome,' . $especialidade->id,
            'codigo' => 'required|string|max:50|unique:especialidades,codigo,' . $especialidade->id,
            'categoria' => 'required|string|max:64',
            'icone' => 'nullable|string|max:64',
            'status' => 'required|in:Ativo,Inativo',
            'profession_id' => 'nullable|exists:professions,id',
        ], [
            'nome.unique' => 'Já existe uma especialidade com este nome.',
            'codigo.unique' => 'Já existe uma especialidade com este código.',
        ]);

        $especialidade->update($data);

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Atualizou a especialidade: {$especialidade->nome} (#{$especialidade->id})",
            'ip_address' => $request->ip(),
            'payload' => $data
        ]);

        return redirect()->route('admin.especialidades.index')->with('success', 'Especialidade atualizada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Especialidade $especialidade): View
    {
        $professions = \App\Models\Profession::all();
        return view('admin.especialidades.index', [
            'especialidades' => Especialidade::with('profession')->orderBy('nome')->paginate(10),
            'professions' => $professions,
            'editingEspecialidade' => $especialidade
        ]);
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Especialidade $especialidade): RedirectResponse
    {
        $especialidade->status = $especialidade->status === 'Ativo' ? 'Inativo' : 'Ativo';
        $especialidade->save();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Alterou o status da especialidade #{$especialidade->id} para {$especialidade->status}",
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', "Especialidade " . ($especialidade->status === 'Ativo' ? 'ativada' : 'desativada') . " com sucesso.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Especialidade $especialidade): RedirectResponse
    {
        $id = $especialidade->id;
        $nome = $especialidade->nome;

        $especialidade->delete();

        AdminLog::create([
            'user_id' => auth()->id(),
            'action' => "Excluiu a especialidade: {$nome} (#{$id})",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.especialidades.index')->with('success', 'Especialidade excluída com sucesso.');
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = ['codigo', 'nome', 'categoria', 'icone', 'status'];
        $example = ['FIT001', 'Personal Trainer', 'Fitness', 'fas fa-dumbbell', 'ativo'];

        return response()->streamDownload(function () use ($headers, $example) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers, ';');
            fputcsv($handle, $example, ';');
            fclose($handle);
        }, 'modelo_especialidades.csv');
    }

    /**
     * Export all specialties to CSV.
     */
    public function export(): StreamedResponse
    {
        $headers = ['codigo', 'nome', 'categoria', 'icone', 'status', 'data_cadastro'];
        $items = Especialidade::all();

        return response()->streamDownload(function () use ($headers, $items) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers, ';');

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->codigo,
                    $item->nome,
                    $item->categoria,
                    $item->icone,
                    $item->status,
                    $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/D',
                ], ';');
            }
            fclose($handle);
        }, 'especialidades_export_' . date('Y-m-d') . '.csv');
    }

    /**
     * Import specialties from CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $handle = fopen($path, 'r');
        $headers = fgetcsv($handle, 0, ';'); // Skip headers

        $errors = [];
        $rowCount = 0;
        $importedCount = 0;
        $dataToInsert = [];

        // Pre-fetch existing to avoid N+1 queries during validation
        $existingCodigos = Especialidade::pluck('codigo')->toArray();
        $existingNomes = Especialidade::pluck('nome')->toArray();

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $rowCount++;
            if (count($row) < 5) {
                $errors[] = "Linha {$rowCount}: Colunas insuficientes.";
                continue;
            }

            $codigo = trim($row[0]);
            $nome = trim($row[1]);
            $categoria = trim($row[2]);
            $icone = trim($row[3]);
            $status = strtolower(trim($row[4]));

            // Validations
            if (empty($codigo)) $errors[] = "Linha {$rowCount}: Código vazio.";
            if (empty($nome)) $errors[] = "Linha {$rowCount}: Nome da especialidade vazio.";
            if (empty($categoria)) $errors[] = "Linha {$rowCount}: Categoria vazia.";
            if (empty($icone)) $errors[] = "Linha {$rowCount}: Ícone vazio.";
            
            if (!in_array($status, ['ativo', 'inativo'])) {
                $errors[] = "Linha {$rowCount}: Status inválido ('{$status}'). Use 'ativo' ou 'inativo'.";
            }

            if (in_array($codigo, $existingCodigos)) {
                $errors[] = "Linha {$rowCount}: Código '{$codigo}' já existe.";
            } else {
                $existingCodigos[] = $codigo;
            }

            if (in_array($nome, $existingNomes)) {
                $errors[] = "Linha {$rowCount}: Nome '{$nome}' já existe.";
            } else {
                $existingNomes[] = $nome;
            }

            if (empty($errors)) {
                $dataToInsert[] = [
                    'codigo' => $codigo,
                    'nome' => $nome,
                    'categoria' => $categoria,
                    'icone' => $icone,
                    'status' => ucfirst($status),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $importedCount++;
            }
        }
        fclose($handle);

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        try {
            if (!empty($dataToInsert)) {
                Especialidade::insert($dataToInsert);
                
                AdminLog::create([
                    'user_id' => auth()->id(),
                    'action' => "Importou {$importedCount} especialidades via arquivo.",
                    'ip_address' => $request->ip(),
                    'payload' => ['count' => $importedCount]
                ]);

                return redirect()->route('admin.especialidades.index')
                    ->with('success', "Importação realizada com sucesso. {$importedCount} registros importados.");
            }
        } catch (\Exception $e) {
            Log::error('Erro na importação de especialidades: ' . $e->getMessage());
            return back()->with('error', 'Erro interno ao processar importação. Verifique se o arquivo está no padrão correto.');
        }

        return back()->with('error', 'Nenhum dado válido encontrado para importação.');
    }
}
