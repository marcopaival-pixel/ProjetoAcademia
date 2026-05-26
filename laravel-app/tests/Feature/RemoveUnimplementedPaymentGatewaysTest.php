<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RemoveUnimplementedPaymentGatewaysTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_removes_stripe_and_pagseguro_settings(): void
    {
        DB::table('payment_settings')->insert([
            [
                'gateway' => 'stripe',
                'status' => 'inactive',
                'environment' => 'sandbox',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gateway' => 'pagseguro',
                'status' => 'inactive',
                'environment' => 'sandbox',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gateway' => 'mercadopago',
                'status' => 'active',
                'environment' => 'sandbox',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $migration = require base_path('database/migrations/2026_05_21_150000_remove_unimplemented_payment_gateways.php');
        $migration->up();

        $this->assertDatabaseMissing('payment_settings', ['gateway' => 'stripe']);
        $this->assertDatabaseMissing('payment_settings', ['gateway' => 'pagseguro']);
        $this->assertDatabaseHas('payment_settings', ['gateway' => 'mercadopago']);
    }
}
