<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_db_orphans_command_succeeds_on_fresh_database(): void
    {
        $this->artisan('app:db:orphans')->assertSuccessful();
    }

    public function test_db_health_report_command_succeeds_on_fresh_database(): void
    {
        $this->artisan('app:db:health-report')->assertSuccessful();
    }

    public function test_db_index_explain_command_succeeds_on_fresh_database(): void
    {
        $this->artisan('app:db:index-explain')->assertSuccessful();
    }

    public function test_db_dead_columns_command_succeeds_on_fresh_database(): void
    {
        $this->artisan('app:db:dead-columns', ['--limit' => 10])->assertSuccessful();
    }

    public function test_purge_pulse_command_succeeds_on_fresh_database(): void
    {
        $this->artisan('app:purge-pulse', ['--force' => true])->assertSuccessful();
    }

    public function test_mysql_health_command_succeeds(): void
    {
        $this->artisan('app:db:mysql-health')->assertSuccessful();
    }

    public function test_backup_verify_command_succeeds_when_folder_missing(): void
    {
        $this->artisan('app:backup:verify', ['--path' => storage_path('app/backups-nonexistent-test')])->assertSuccessful();
    }
}
