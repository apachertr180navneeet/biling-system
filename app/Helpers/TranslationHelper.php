<?php

namespace App\Helpers;

use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

class TranslationHelper
{
    public static function get(string $key, string $group = 'messages', string $default = null): string
    {
        $locale = app()->getLocale();

        $cacheKey = "translation_{$locale}_{$group}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $group, $locale, $default) {
            $translation = Translation::where('group', $group)
                ->where('key', $key)
                ->whereHas('language', function ($q) use ($locale) {
                    $q->where('code', $locale);
                })
                ->first();

            return $translation?->value ?? $default ?? $key;
        });
    }

    public static function clearCache(): void
    {
        $locale = app()->getLocale();
        Cache::forget("translation_{$locale}_messages_*");
    }
}
