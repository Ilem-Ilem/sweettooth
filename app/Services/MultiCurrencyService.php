<?php

namespace App\Services;

use App\Helpers\Settings;
use App\Models\GlEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MultiCurrencyService
{
    private string $baseCurrency;

    public function __construct()
    {
        $this->baseCurrency = Settings::currencyLocalization('primary_currency', 'NGN');
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(
        float $amount,
        string $fromCurrency,
        string $toCurrency,
        Carbon $date = null
    ): float {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $date = $date ?? now();
        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency, $date);

        return floatval($amount * $exchangeRate);
    }

    /**
     * Get exchange rate for date
     */
    public function getExchangeRate(
        string $fromCurrency,
        string $toCurrency,
        Carbon $date = null
    ): float {
        // TODO: Implement actual exchange rate retrieval
        // Could integrate with external API or database storage

        // Mock rates for demonstration
        $rates = [
            'USD-EUR' => 0.92,
            'EUR-USD' => 1.09,
            'USD-GBP' => 0.79,
            'GBP-USD' => 1.27,
            'USD-JPY' => 110.50,
            'JPY-USD' => 0.0091,
        ];

        $key = "{$fromCurrency}-{$toCurrency}";
        return $rates[$key] ?? 1.0;
    }

    /**
     * Record multi-currency transaction
     */
    public function recordMultiCurrencyTransaction(
        GlEntry $entry,
        string $transactionCurrency,
        string $accountingCurrency = null
    ): array {
        $accountingCurrency = $accountingCurrency ?? $this->baseCurrency;

        if ($transactionCurrency === $accountingCurrency) {
            return [
                'original_amount' => floatval($entry->debit) ?: floatval($entry->credit),
                'original_currency' => $transactionCurrency,
                'converted_amount' => floatval($entry->debit) ?: floatval($entry->credit),
                'converted_currency' => $accountingCurrency,
                'exchange_rate' => 1.0,
                'exchange_gain_loss' => 0,
            ];
        }

        $originalAmount = floatval($entry->debit) ?: floatval($entry->credit);
        $exchangeRate = $this->getExchangeRate($transactionCurrency, $accountingCurrency);
        $convertedAmount = floatval($originalAmount * $exchangeRate);

        return [
            'original_amount' => $originalAmount,
            'original_currency' => $transactionCurrency,
            'converted_amount' => $convertedAmount,
            'converted_currency' => $accountingCurrency,
            'exchange_rate' => $exchangeRate,
            'exchange_gain_loss' => 0, // Calculated at period end
        ];
    }

    /**
     * Calculate unrealized exchange gain/loss at period end
     */
    public function calculateUnrealizedExchangeGainLoss(
        string $currency,
        float $balance,
        string $accountingCurrency = null
    ): float {
        $accountingCurrency = $accountingCurrency ?? $this->baseCurrency;

        if ($currency === $accountingCurrency) {
            return 0;
        }

        // Get current exchange rate
        $currentRate = $this->getExchangeRate($currency, $accountingCurrency);

        // Get historical rate (would need to be stored)
        $historicalRate = 1.0; // Placeholder

        // Calculate gain/loss
        $convertedAtCurrent = floatval($balance * $currentRate);
        $convertedAtHistorical = floatval($balance * $historicalRate);

        return floatval($convertedAtCurrent - $convertedAtHistorical);
    }

    /**
     * Generate multi-currency financial report
     */
    public function generateMultiCurrencyReport(
        array $currencies,
        $period = null,
        string $reportingCurrency = null
    ): array {
        $reportingCurrency = $reportingCurrency ?? $this->baseCurrency;

        $report = [];

        foreach ($currencies as $currency) {
            // Get entries in this currency
            $entries = $this->getEntriesByCurrency($currency, $period);

            // Convert to reporting currency
            $convertedEntries = $entries->map(function ($entry) use ($currency, $reportingCurrency) {
                $debit = floatval($entry['debit']);
                $credit = floatval($entry['credit']);

                $convertedDebit = $this->convert($debit, $currency, $reportingCurrency);
                $convertedCredit = $this->convert($credit, $currency, $reportingCurrency);

                return [
                    'original_debit' => $debit,
                    'original_credit' => $credit,
                    'converted_debit' => $convertedDebit,
                    'converted_credit' => $convertedCredit,
                ];
            });

            $report[$currency] = [
                'currency' => $currency,
                'exchange_rate_to_reporting' => $this->getExchangeRate($currency, $reportingCurrency),
                'entries_count' => $entries->count(),
                'total_debits' => $convertedEntries->sum('converted_debit'),
                'total_credits' => $convertedEntries->sum('converted_credit'),
            ];
        }

        return [
            'reporting_currency' => $reportingCurrency,
            'currencies' => $report,
            'consolidation_total_debits' => collect($report)->sum('total_debits'),
            'consolidation_total_credits' => collect($report)->sum('total_credits'),
        ];
    }

    /**
     * Get entries by currency (placeholder)
     */
    private function getEntriesByCurrency(string $currency, $period = null): Collection
    {
        // TODO: Implement currency tracking on GL entries
        // For now, return empty collection
        return collect();
    }

    /**
     * Set base currency
     */
    public function setBaseCurrency(string $currency): void
    {
        $this->baseCurrency = $currency;
    }

    /**
     * Get base currency
     */
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'CHF' => 'Swiss Franc',
            'CNY' => 'Chinese Yuan',
            'INR' => 'Indian Rupee',
            'MXN' => 'Mexican Peso',
        ];
    }

    /**
     * Perform currency revaluation at period end
     */
    public function performCurrencyRevaluation($period): array
    {
        // TODO: Implement actual revaluation logic
        // This would create GL entries for exchange gains/losses

        return [
            'period' => $period->getDisplayName(),
            'revaluations' => [],
            'total_gain_loss' => 0,
            'status' => 'pending',
        ];
    }
}
