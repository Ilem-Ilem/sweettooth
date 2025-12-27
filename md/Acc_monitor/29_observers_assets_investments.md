# Observers for Assets and Investments Integration

This guide explains how observers will automate asset management and investment tracking, including depreciation, amortization, and portfolio updates.

## Assets/Investments Observers

### 1. FixedAssetObserver

Handles asset acquisition and depreciation.

```php
class FixedAssetObserver
{
    public function created(FixedAsset $asset): void
    {
        $this->createInitialJournalEntry($asset);
        $this->scheduleDepreciation($asset);
    }

    public function updated(FixedAsset $asset): void
    {
        if ($asset->wasChanged('status') && $asset->status === 'disposed') {
            $this->processDisposal($asset);
        }
    }

    private function createInitialJournalEntry(FixedAsset $asset): void
    {
        // Debit Fixed Asset, Credit Cash/Bank
    }

    private function scheduleDepreciation(FixedAsset $asset): void
    {
        // Schedule monthly depreciation jobs
    }

    private function processDisposal(FixedAsset $asset): void
    {
        // Create disposal journal entries
        // Remove from depreciation schedule
    }
}
```

### 2. DepreciationEntryObserver

Manages automatic depreciation posting.

```php
class DepreciationEntryObserver
{
    protected GlPostingService $glPostingService;

    public function created(DepreciationEntry $entry): void
    {
        $this->postToGL($entry);
        $this->updateAssetValue($entry);
    }

    private function postToGL(DepreciationEntry $entry): void
    {
        try {
            $this->glPostingService->postDepreciation($entry);
        } catch (Exception $e) {
            \Log::error('Failed to post depreciation', [
                'entry_id' => $entry->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateAssetValue(DepreciationEntry $entry): void
    {
        $entry->fixedAsset->increment('accumulated_depreciation', $entry->depreciation_amount);
        $entry->fixedAsset->update([
            'current_value' => $entry->fixedAsset->purchase_price - $entry->fixedAsset->accumulated_depreciation
        ]);
    }
}
```

### 3. IntangibleAssetObserver

Similar to fixed assets but for intangibles.

```php
class IntangibleAssetObserver
{
    public function created(IntangibleAsset $asset): void
    {
        $this->createInitialJournalEntry($asset);
        $this->scheduleAmortization($asset);
    }
}
```

### 4. AmortizationEntryObserver

Handles amortization posting.

```php
class AmortizationEntryObserver
{
    public function created(AmortizationEntry $entry): void
    {
        $this->postToGL($entry);
        $this->updateAssetValue($entry);
    }

    private function postToGL(AmortizationEntry $entry): void
    {
        // Similar to depreciation
    }

    private function updateAssetValue(AmortizationEntry $entry): void
    {
        $entry->intangibleAsset->increment('accumulated_amortization', $entry->amortization_amount);
        // Update current value
    }
}
```

### 5. InvestmentObserver

Manages investment tracking and valuation.

```php
class InvestmentObserver
{
    public function updated(Investment $investment): void
    {
        if ($investment->wasChanged('quantity')) {
            $this->updateValuation($investment);
            $this->createTransactionRecord($investment);
        }
    }

    private function updateValuation(Investment $investment): void
    {
        $currentValue = $investment->currentValue();
        $investment->update(['current_valuation' => $currentValue]);
    }

    private function createTransactionRecord(Investment $investment): void
    {
        // Record buy/sell transactions
    }
}
```

### 6. InvestmentMarketPriceObserver

Updates portfolio valuations.

```php
class InvestmentMarketPriceObserver
{
    public function created(InvestmentMarketPrice $price): void
    {
        $this->updateInvestmentValue($price);
        $this->calculateUnrealizedGains($price);
    }

    private function updateInvestmentValue(InvestmentMarketPrice $price): void
    {
        $price->investment->update([
            'current_market_price' => $price->price
        ]);
    }

    private function calculateUnrealizedGains(InvestmentMarketPrice $price): void
    {
        $gains = $price->investment->unrealizedGainLoss();
        $price->investment->update(['unrealized_gains' => $gains]);
    }
}
```

## Scheduled Commands for Automation

```php
// Monthly depreciation
class RunMonthlyDepreciation extends Command
{
    public function handle()
    {
        $assets = FixedAsset::active()->get();
        
        foreach ($assets as $asset) {
            $depreciation = $asset->calculateDepreciation(now());
            
            DepreciationEntry::create([
                'fixed_asset_id' => $asset->id,
                'depreciation_date' => now(),
                'depreciation_amount' => $depreciation,
            ]);
        }
    }
}

// Investment price updates
class UpdateInvestmentPrices extends Command
{
    public function handle()
    {
        // Fetch prices from API and create InvestmentMarketPrice records
    }
}
```

## Registration

```php
public function boot()
{
    FixedAsset::observe(FixedAssetObserver::class);
    DepreciationEntry::observe(DepreciationEntryObserver::class);
    IntangibleAsset::observe(IntangibleAssetObserver::class);
    AmortizationEntry::observe(AmortizationEntryObserver::class);
    Investment::observe(InvestmentObserver::class);
    InvestmentMarketPrice::observe(InvestmentMarketPriceObserver::class);
}
```

## Benefits

- Automatic depreciation/amortization calculations
- Real-time investment valuations
- Consistent GL posting for asset transactions
- Proactive asset lifecycle management

This ensures accurate asset and investment accounting with minimal manual effort.