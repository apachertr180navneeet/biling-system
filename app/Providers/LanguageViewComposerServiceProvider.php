<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Models\Language;
use Illuminate\Support\ServiceProvider;

class LanguageViewComposerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('availableLanguages', Language::where('status', 'active')->get());
            $view->with('currentLocale', app()->getLocale());
        });
    }
}
