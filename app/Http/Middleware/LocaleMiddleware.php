<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Language;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') && $request->has('switch_language')) {
            $langCode = $request->input('locale');
            if ($langCode && Language::where('code', $langCode)->where('status', 'active')->exists()) {
                session(['locale' => $langCode]);
                app()->setLocale($langCode);
            }
            return redirect()->back();
        }

        $locale = session('locale');
        if (!$locale) {
            $default = Language::where('is_default', true)->where('status', 'active')->first();
            $locale = $default?->code ?? 'en';
            session(['locale' => $locale]);
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
