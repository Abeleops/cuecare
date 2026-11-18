<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public function record(string $action, ?Model $subject = null, array $metadata = []): void
    {
        AuditLog::query()->create(['user_id' => auth()->id(), 'action' => $action, 'subject_type' => $subject?->getMorphClass(), 'subject_id' => $subject?->getKey(), 'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(), 'metadata' => $metadata]);
    }
}
