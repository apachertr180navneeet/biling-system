<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Tax;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['web.*', 'admin.*'], function ($view) {
            $company = Company::first();
            $view->with('company', $company);
            $view->with('currencySymbol', $this->resolveCurrencySymbol($company));
            $view->with('taxRate', $this->resolveTaxRate());
        });
    }

    private function resolveCurrencySymbol(?Company $company): string
    {
        $companyCurrency = $company ? $company->currency : null;
        if ($companyCurrency && $companyCurrency->status === 'active') {
            return $companyCurrency->symbol;
        }
        return Currency::where('is_default', true)
            ->where('status', 'active')
            ->value('symbol') ?? '$';
    }

    private function resolveTaxRate(): float
    {
        $defaultTax = Tax::where('is_default', true)
            ->where('status', 'active')
            ->where('type', 'percentage')
            ->first();

        return $defaultTax ? (float) $defaultTax->rate : 12.0;
    }
}
