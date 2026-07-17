<?php

namespace App\Enums;

enum PdfSignatureMode: string
{
    case Manual = 'manual';
    case Upload = 'upload';
}
