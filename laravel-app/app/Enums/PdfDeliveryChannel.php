<?php

namespace App\Enums;

enum PdfDeliveryChannel: string
{
    case Email = 'email';
    case Whatsapp = 'whatsapp';
}
