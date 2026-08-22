<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    public static function log(
        string $action,
        string $modelType,
        ?int $modelId = null,
        ?string $description = null,
        ?array $metadata = null,
        ?int $companyId = null
    ): AuditLog {
        return AuditLog::create([
            'user_id'       => Auth::id(),
            'company_id'    => $companyId ?? (Auth::user()->current_company_id ?? null),
            'action'        => $action,
            'model_type'    => $modelType,
            'model_id'      => $modelId,
            'description'   => $description,
            'metadata'      => $metadata,
            'ip_address'    => request()->ip(),
        ]);
    }
}
