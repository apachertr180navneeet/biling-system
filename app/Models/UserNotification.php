<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'module', 'url', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function markAllAsRead(int $userId): void
    {
        static::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public static function send(int $userId, string $title, string $message, string $type = 'info', ?string $module = null, ?string $url = null): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'module' => $module,
            'url' => $url,
        ]);
    }

    public static function sendToRole(string $roleSlug, string $title, string $message, string $type = 'info', ?string $module = null, ?string $url = null): void
    {
        $users = User::whereHas('roleDetail', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        })->where('status', 'active')->get();

        foreach ($users as $user) {
            static::send($user->id, $title, $message, $type, $module, $url);
        }
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
