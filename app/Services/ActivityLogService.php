<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public static function log(
        string $description,
        $subject = null,
        string $event = 'created',
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $module = null
    ): ActivityLog {
        return ActivityLog::create([
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'causer_id' => auth()->id(),
            'causer_type' => auth()->user() ? get_class(auth()->user()) : null,
            'properties' => [
                'module' => $module ?? 'system',
                'model' => $subject ? class_basename($subject) : null,
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    public static function getLogs($subjectType = null, $subjectId = null, int $limit = 50)
    {
        $query = ActivityLog::with('causer');

        if ($subjectType && $subjectId) {
            $query->where('subject_type', $subjectType)->where('subject_id', $subjectId);
        }

        return $query->latest('created_at')->limit($limit)->get();
    }

    public static function getLogsByModule(string $module, int $limit = 50)
    {
        return ActivityLog::where('properties->module', $module)
            ->with('causer', 'subject')
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }
}
