<?php

namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public static function convert(float $amount, string $fromCode, string $toCode): float
    {
        if ($fromCode === $toCode) return $amount;

        $from = Currency::where('code', $fromCode)->first();
        $to = Currency::where('code', $toCode)->first();

        if (!$from || !$to) return $amount;

        $baseAmount = $amount / ($from->exchange_rate ?: 1);
        return round($baseAmount * ($to->exchange_rate ?: 1), 2);
    }

    public static function getDefaultCurrency(): ?Currency
    {
        return Currency::where('is_default', true)->first();
    }

    public static function format(float $amount, ?string $currencyCode = null): string
    {
        $currency = $currencyCode
            ? Currency::where('code', $currencyCode)->first()
            : static::getDefaultCurrency();

        $symbol = $currency?->symbol ?? '$';
        return $symbol . number_format($amount, 2);
    }

    public static function getAllActive(): \Illuminate\Support\Collection
    {
        return Currency::where('status', 'active')->orderBy('code')->get();
    }
}
