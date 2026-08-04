<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    public function record(string $action, ?Model $subject = null, array $before = [], array $after = [], array $metadata = [], ?Request $request = null): AuditLog
    {
        return AuditLog::query()->create([
            'actor_id' => $request?->user()?->getKey(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject ? (string) $subject->getKey() : null,
            'request_id' => $request?->attributes->get('request_id'),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'before' => $before ?: null,
            'after' => $after ?: null,
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);
    }
}
