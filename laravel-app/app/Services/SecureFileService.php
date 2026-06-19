<?php

namespace App\Services;

use App\Models\User;
use App\Support\PatientAccessGuard;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileService
{
    public const PRIVATE_DISK = 'local';

    public function storeSensitiveFile(\Illuminate\Http\UploadedFile $file, string $directory): string
    {
        return $file->store($directory, self::PRIVATE_DISK);
    }

    public function deleteFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach ([self::PRIVATE_DISK, 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }

    public function streamForOwner(User $user, string $path, int $ownerUserId, ?string $disk = null): StreamedResponse
    {
        PatientAccessGuard::assertStudentDataAccess($user, $ownerUserId);

        return $this->streamPath($path, $disk);
    }

    public function streamPath(string $path, ?string $disk = null): StreamedResponse
    {
        $resolvedDisk = $disk ?? $this->resolveDisk($path);

        if (! Storage::disk($resolvedDisk)->exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk($resolvedDisk)->response($path);
    }

    public function resolveDisk(string $path): string
    {
        if (Storage::disk(self::PRIVATE_DISK)->exists($path)) {
            return self::PRIVATE_DISK;
        }

        return 'public';
    }

    public function secureUrl(string $type, int $id): string
    {
        return route('secure-files.show', ['type' => $type, 'id' => $id]);
    }
}
