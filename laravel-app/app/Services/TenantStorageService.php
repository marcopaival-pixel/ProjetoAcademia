<?php

namespace App\Services;

use App\Models\AcademyCompany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantStorageService
{
    /**
     * Armazena um arquivo em um diretório isolado por tenant.
     */
    public function store(AcademyCompany $company, UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): string
    {
        $tenantId = $company->id;
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        $path = "tenants/{$tenantId}/{$folder}";
        
        return $file->storeAs($path, $filename, $disk);
    }

    /**
     * Remove um arquivo do storage do tenant.
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        return false;
    }

    /**
     * Retorna a URL pública de um arquivo do tenant.
     */
    public function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }

    /**
     * Lista todos os arquivos de uma pasta do tenant.
     */
    public function listFiles(AcademyCompany $company, string $folder = 'uploads', string $disk = 'public'): array
    {
        $tenantId = $company->id;
        $path = "tenants/{$tenantId}/{$folder}";
        
        return Storage::disk($disk)->files($path);
    }
}
