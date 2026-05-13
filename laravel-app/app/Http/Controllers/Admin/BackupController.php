<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Carbon\Carbon;
use App\Models\AcademyCompany;

class BackupController extends Controller
{
    public function index()
    {
        $backups = [];
        $disks = ['local', 'backup'];
        $companies = AcademyCompany::orderBy('name')->get();

        foreach ($disks as $disk) {
            $files = Storage::disk($disk)->files(config('backup.backup.name'));

            foreach ($files as $file) {
                if (str_ends_with($file, '.zip')) {
                    $backups[] = [
                        'file_name' => str_replace(config('backup.backup.name') . '/', '', $file),
                        'file_size' => $this->formatBytes(Storage::disk($disk)->size($file)),
                        'last_modified' => Carbon::createFromTimestamp(Storage::disk($disk)->lastModified($file))->format('d/m/Y H:i:s'),
                        'disk' => $disk,
                        'path' => $file,
                    ];
                }
            }
        }

        // Sort by last modified descending
        usort($backups, function ($a, $b) {
            return strtotime($b['last_modified']) <=> strtotime($a['last_modified']);
        });

        return view('admin.backups.index', compact('backups', 'companies'));
    }

    public function create()
    {
        try {
            // Tentamos rodar o backup completo via Spatie se o comando existir
            Artisan::call('backup:run');
            return redirect()->back()->with('success', 'Processo de backup completo iniciado com sucesso!');
        } catch (\Exception $e) {
            // Se o comando não existir ou falhar (ex: falta de vendor), usamos o backup nativo
            Log::warning('Spatie Backup indisponível ou falhou, usando fallback nativo: ' . $e->getMessage());
            return $this->createNativeBackup();
        }
    }

    protected function createNativeBackup()
    {
        try {
            $dbConfig = config('database.connections.mysql');
            $fileName = 'native_db_backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $storagePath = storage_path('app/backups/' . $fileName);
            
            if (!file_exists(dirname($storagePath))) {
                mkdir(dirname($storagePath), 0755, true);
            }

            // Detect mysqldump path (common in XAMPP Windows)
            $mysqldump = 'mysqldump';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                if (file_exists('C:\xampp\mysql\bin\mysqldump.exe')) {
                    $mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';
                }
            }

            // Command for mysqldump (using --result-file instead of > for cross-platform stability)
            $passwordPart = $dbConfig['password'] ? "--password=" . escapeshellarg($dbConfig['password']) : "";
            
            $command = sprintf(
                '%s --user=%s %s --host=%s --result-file=%s %s',
                escapeshellarg($mysqldump),
                escapeshellarg($dbConfig['username']),
                $passwordPart,
                escapeshellarg($dbConfig['host']),
                escapeshellarg($storagePath),
                escapeshellarg($dbConfig['database'])
            );

            $result = Process::run($command);

            if (!$result->successful()) {
                $errorMsg = $result->errorOutput() ?: "Código " . $result->exitCode();
                throw new \Exception("Erro ao executar mysqldump: $errorMsg");
            }

            return redirect()->back()->with('success', "Backup nativo do banco de dados gerado com sucesso: {$fileName}");

        } catch (\Exception $e) {
            Log::error('Native backup failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Falha crítica ao gerar backup: ' . $e->getMessage());
        }
    }

    public function download($disk, $fileName)
    {
        $path = config('backup.backup.name') . '/' . $fileName;

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->download($path);
        }

        return redirect()->back()->with('error', 'Ficheiro não encontrado.');
    }

    public function delete($disk, $fileName)
    {
        $path = config('backup.backup.name') . '/' . $fileName;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
            return redirect()->back()->with('success', 'Backup removido com sucesso.');
        }

        return redirect()->back()->with('error', 'Erro ao remover backup.');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'disk' => 'required',
            'file_name' => 'required',
        ]);

        try {
            return redirect()->back()->with('warning', 'A restauração automática via painel está em fase experimental. Recomenda-se restauração manual via CLI para maior segurança.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao restaurar: ' . $e->getMessage());
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
