<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuditLogService
{
    private const SENSITIVE_FIELDS = [
        'password', 'password_confirmation', 'remember_token', 'token', 'token_hash',
        'secret', 'api_key', 'access_token', 'refresh_token', 'file_url',
    ];

    public function updated(Model $model): void
    {
        $changes = Arr::except($model->getChanges(), ['updated_at']);
        if ($changes === []) {
            return;
        }

        $before = collect(array_keys($changes))
            ->mapWithKeys(fn (string $field) => [$field => $model->getOriginal($field)])
            ->all();

        $this->write('updated', $model, [
            'before' => $this->sanitize($before),
            'after' => $this->sanitize($changes),
        ]);
    }

    public function deleted(Model $model): void
    {
        $this->write('deleted', $model, [
            'snapshot' => $this->sanitize(Arr::except($model->getAttributes(), ['updated_at'])),
        ]);
    }

    private function write(string $action, Model $model, array $details): void
    {
        $request = app()->bound('request') ? request() : null;
        $actor = Auth::user();

        Log::channel('audit')->info("model.{$action}", array_merge([
            'action' => $action,
            'resource' => class_basename($model),
            'resource_id' => $model->getKey(),
            'actor_id' => $actor?->getAuthIdentifier(),
            'actor_email' => $actor?->email,
            'actor_role' => $actor?->role,
            'route' => $request?->route()?->getName(),
            'method' => $request?->method(),
            'ip' => $request?->ip(),
        ], $details));
    }

    private function sanitize(array $values): array
    {
        foreach ($values as $field => &$value) {
            $normalized = strtolower((string) $field);
            if (in_array($normalized, self::SENSITIVE_FIELDS, true)
                || Str::contains($normalized, ['password', 'token', 'secret', 'api_key'])) {
                $value = '[REDACTED]';
            }
        }

        return $values;
    }
}
