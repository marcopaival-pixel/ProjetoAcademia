<?php

namespace App\Services;

use App\Models\SystemAccessLink;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SystemAccessService
{
    /**
     * Generate access link for a user.
     *
     * @param User $user
     * @param string|null $systemName
     * @return SystemAccessLink
     */
    public function generateForUser(User $user, ?string $systemName = null): SystemAccessLink
    {
        $systemName = $systemName ?: config('app.name', 'NexShape');
        $loginUrl = getSystemLoginUrl();
        
        // Generate QR Code
        $qrCodePath = $this->generateQrCode($loginUrl, $user->id);

        return SystemAccessLink::updateOrCreate(
            ['user_id' => $user->id, 'system_name' => $systemName],
            [
                'system_url' => $loginUrl,
                'qr_code_path' => $qrCodePath,
            ]
        );
    }

    /**
     * Generate QR Code for a URL.
     *
     * @param string $url
     * @param int $userId
     * @return string
     */
    protected function generateQrCode(string $url, int $userId): string
    {
        $qrCode = QrCode::create($url);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $fileName = 'qrcode_user_' . $userId . '_' . Str::random(8) . '.png';
        $path = config('system.qr_code.path', 'qrcodes/access/') . $fileName;
        $disk = config('system.qr_code.disk', 'public');

        Storage::disk($disk)->put($path, $result->getString());

        return $path;
    }
}
