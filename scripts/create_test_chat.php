<?php
require 'laravel-app/vendor/autoload.php';
$app = require_once 'laravel-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\OmniCompany;
use App\Models\OmniChannel;
use App\Models\OmniConversation;
use App\Models\OmniMessage;

try {
    $company = OmniCompany::where('slug', 'empresa-modelo')->first();
    if (!$company) die("Execute /setup-omni primeiro no navegador.\n");

    $channel = OmniChannel::where('company_id', $company->id)->first();

    $conv = OmniConversation::create([
        'company_id' => $company->id,
        'channel_id' => $channel->id,
        'customer_external_id' => 'user_test_123',
        'customer_name' => 'João Teste',
        'status' => 'bot',
        'last_message_at' => now()
    ]);

    OmniMessage::create([
        'conversation_id' => $conv->id,
        'sender_type' => 'customer',
        'content' => 'Olá! Gostaria de saber os preços.',
        'content_type' => 'text'
    ]);

    echo "✅ Conversa de teste criada com sucesso!\n";
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
