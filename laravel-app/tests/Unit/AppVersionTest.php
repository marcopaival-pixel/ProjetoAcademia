<?php

namespace Tests\Unit;

use App\Support\AppVersion;
use PHPUnit\Framework\TestCase;

class AppVersionTest extends TestCase
{
    private string $tmpRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'app-version-test-' . uniqid();
        mkdir($this->tmpRoot);
        AppVersion::useRootForTesting($this->tmpRoot);
    }

    protected function tearDown(): void
    {
        AppVersion::useRootForTesting(null);
        if (is_dir($this->tmpRoot)) {
            @unlink($this->tmpRoot . DIRECTORY_SEPARATOR . 'VERSION');
            @unlink($this->tmpRoot . DIRECTORY_SEPARATOR . 'CHANGELOG.md');
            @rmdir($this->tmpRoot);
        }
        parent::tearDown();
    }

    public function test_parses_and_normalizes_version(): void
    {
        $this->assertSame('1.2.3', AppVersion::normalize('v1.2.3'));
        $this->assertSame([1, 2, 3], AppVersion::parse('1.2.3'));
        $this->assertSame([1, 1, 0], AppVersion::parse('1.1.0-rc1'));
    }

    public function test_bump_patch_increments_patch(): void
    {
        AppVersion::write('1.0.0');
        $this->assertSame('1.0.1', AppVersion::bump('patch'));
        $this->assertSame('1.0.1', AppVersion::current());
    }

    public function test_bump_minor_resets_patch(): void
    {
        AppVersion::write('1.0.5');
        $this->assertSame('1.1.0', AppVersion::bump('minor'));
    }
}
