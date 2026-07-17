<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureFileService
{
    /**
     * Store sensitive uploads on the local private disk.
     */
    public function storeSensitiveFile(UploadedFile $file, string $folder = 'uploads'): string
    {
        $folder = trim($folder, '/');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $filename = Str::uuid()->toString().'.'.$extension;

        return $file->storeAs($folder, $filename, 'local');
    }

    public function exists(string $path): bool
    {
        return Storage::disk('local')->exists($path);
    }

    public function path(string $path): string
    {
        return Storage::disk('local')->path($path);
    }

    public function delete(string $path): bool
    {
        return Storage::disk('local')->delete($path);
    }
}
