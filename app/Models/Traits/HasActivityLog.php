<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

trait HasActivityLog
{
    protected static function bootHasActivityLog(): void
    {
        static::created(function ($model) {
            $model->logActivity('created', null, $model->toArray());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            $original = $model->getOriginal();
            $old = [];
            foreach ($dirty as $key => $value) {
                $old[$key] = $original[$key] ?? null;
            }
            $model->logActivity('updated', $old, $dirty);
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->toArray(), null);
        });
    }

    public function logActivity(string $event, ?array $oldValues = null, ?array $newValues = null): void
    {
        $module = $this->getActivityModule();

        ActivityLog::create([
            'description' => class_basename(static::class) . " was {$event}",
            'subject_type' => static::class,
            'subject_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'causer_id' => auth()->id(),
            'causer_type' => get_class(auth()->user() ?? new \stdClass),
            'properties' => [
                'module' => $module,
                'model' => class_basename(static::class),
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    protected function getActivityModule(): string
    {
        $class = class_basename(static::class);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
}
