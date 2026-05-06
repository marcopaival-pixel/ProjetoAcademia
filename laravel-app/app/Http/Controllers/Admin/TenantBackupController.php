<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantBackupService;
use App\Models\AcademyCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TenantBackupController extends Controller
{
    protected TenantBackupService $backupService;

    public function __construct(TenantBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function index($companyId)
    {
        $company = AcademyCompany::findOrFail($companyId);
        $backups = [];
        $directory = "backups/tenants/{$companyId}";

        if (Storage::exists($directory)) {
            $files = Storage::files($directory);
            foreach ($files as $file) {
                if (str_ends_with($file, '.json')) {
                    $backups[] = [
                        'file_name' => basename($file),
                        'file_size' => $this->formatBytes(Storage::size($file)),
                        'last_modified' => Carbon::createFromTimestamp(Storage::lastModified($file))->format('d/m/Y H:i:s'),
                        'path' => $file,
                    ];
                }
            }
        }

        // Sort by last modified descending
        usort($backups, function ($a, $b) {
            return strtotime($b['last_modified']) <=> strtotime($a['last_modified']);
        });

        return view('admin.backups.tenant', compact('company', 'backups'));
    }

    public function create($companyId)
    {
        try {
            $result = $this->backupService->export($companyId);
            return redirect()->back()->with('success', "Backup da clínica gerado com sucesso: {$result['file_name']}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Erro ao gerar backup: " . $e->getMessage());
        }
    }

    public function download($companyId, $fileName)
    {
        $path = "backups/tenants/{$companyId}/{$fileName}";

        if (Storage::exists($path)) {
            return Storage::download($path);
        }

        return redirect()->back()->with('error', 'Ficheiro não encontrado.');
    }

    public function delete($companyId, $fileName)
    {
        $path = "backups/tenants/{$companyId}/{$fileName}";

        if (Storage::exists($path)) {
            Storage::delete($path);
            return redirect()->back()->with('success', 'Backup removido com sucesso.');
        }

        return redirect()->back()->with('error', 'Erro ao remover backup.');
    }

    public function restore(Request $request, $companyId)
    {
        $request->validate([
            'file_name' => 'required',
        ]);

        try {
            $path = "backups/tenants/{$companyId}/{$request->file_name}";
            $this->backupService->restore($companyId, $path);
            
            return redirect()->back()->with('success', "Restauração da clínica concluída com sucesso!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Erro ao restaurar: " . $e->getMessage());
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
