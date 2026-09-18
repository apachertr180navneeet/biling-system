<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id', 'group', 'key', 'value',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public static function getTranslation(string $code, string $key, string $group = 'messages'): ?string
    {
        $language = Language::where('code', $code)->first();
        if (!$language) return null;

        return static::where('language_id', $language->id)
            ->where('group', $group)
            ->where('key', $key)
            ->value('value');
    }

    public static function getAllTranslations(string $code, string $group = 'messages'): array
    {
        $language = Language::where('code', $code)->first();
        if (!$language) return [];

        return static::where('language_id', $language->id)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
