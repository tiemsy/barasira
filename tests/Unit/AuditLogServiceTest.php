<?php

namespace Tests\Unit;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuditLogServiceTest extends TestCase
{
    public function test_update_audit_contains_changes_and_redacts_secrets(): void
    {
        $path = sys_get_temp_dir().'/barasira-audit-test-'.uniqid().'.log';
        config(['logging.channels.audit' => ['driver' => 'single', 'path' => $path, 'level' => 'info']]);
        Log::forgetChannel('audit');

        $model = new class extends Model
        {
            protected $guarded = [];
        };
        $model->forceFill(['id' => 42, 'name' => 'Avant', 'password' => 'ancien-secret']);
        $model->syncOriginal();
        $model->forceFill(['name' => 'Après', 'password' => 'nouveau-secret']);
        $model->syncChanges();

        app(AuditLogService::class)->updated($model);
        $contents = file_get_contents($path);

        $this->assertStringContainsString('model.updated', $contents);
        $this->assertStringContainsString('Avant', $contents);
        $this->assertStringContainsString('Après', $contents);
        $this->assertStringContainsString('[REDACTED]', $contents);
        $this->assertStringNotContainsString('nouveau-secret', $contents);
        unlink($path);
    }
}
