# 07_LOCALIZATION_SETTINGS_APPLICATION.md

## Localization Settings Application

### Current Issues with Localization Usage

#### 1. Date Formatting
**Problem Areas:**
- Date displays use hardcoded formats
- Date inputs don't respect user preferences
- Report dates not using configured format

**Current Issues:**
```php
// Multiple files have hardcoded date formats
// Example: app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php
$formattedDate = $date->format('Y-m-d'); // Should use setting

// app/Livewire/Inventory/Purchases.php
$purchases->created_at->format('M d, Y'); // Should use setting

// app/Livewire/Production/DailyProduce/Index.php
$production->date->format('d/m/Y'); // Should use setting
```

**Required Changes:**
```php
// Add date formatting helper trait
// app/Traits/DateFormattingTrait.php
trait DateFormattingTrait
{
    protected function formatDate($date, $format = null): string
    {
        if (!$date) return '';
        
        $dateFormat = $format ?? Settings::currencyLocalization('date_format', 'MM/DD/YYYY');
        
        // Convert format to PHP date format
        $phpFormat = $this->convertToPhpDateFormat($dateFormat);
        
        return $date->format($phpFormat);
    }
    
    protected function convertToPhpDateFormat(string $format): string
    {
        $formatMap = [
            'MM/DD/YYYY' => 'm/d/Y',
            'DD/MM/YYYY' => 'd/m/Y',
            'YYYY-MM-DD' => 'Y-m-d',
            'DD-MM-YYYY' => 'd-m-Y',
            'YYYY/MM/DD' => 'Y/m/d',
            'MMM DD, YYYY' => 'M d, Y',
            'DD MMM YYYY' => 'd M Y'
        ];
        
        return $formatMap[$format] ?? 'm/d/Y';
    }
    
    protected function getDatePickerFormat(string $format = null): string
    {
        $dateFormat = $format ?? Settings::currencyLocalization('date_format', 'MM/DD/YYYY');
        
        // Convert to datepicker format
        $formatMap = [
            'MM/DD/YYYY' => 'mm/dd/yyyy',
            'DD/MM/YYYY' => 'dd/mm/yyyy',
            'YYYY-MM-DD' => 'yyyy-mm-dd',
            'DD-MM-YYYY' => 'dd-mm-yyyy',
            'YYYY/MM/DD' => 'yyyy/mm/dd',
            'MMM DD, YYYY' => 'M d, yyyy',
            'DD MMM YYYY' => 'd M yyyy'
        ];
        
        return $formatMap[$dateFormat] ?? 'mm/dd/yyyy';
    }
}
```

#### 2. Language Localization
**Problem Areas:**
- Text not localized
- Language preferences not respected
- Error messages in single language

**Required Changes:**
```php
// Add language detection middleware
// app/Http/Middleware/SetLocale.php
public function handle($request, Closure $next)
{
    $language = $this->getUserLanguage();
    app()->setLocale($language);
    
    return $next($request);
}

private function getUserLanguage(): string
{
    // Try to get from user settings first
    if (auth()->check()) {
        $userLanguage = auth()->user()->language;
        if ($userLanguage) {
            return $userLanguage;
        }
    }
    
    // Fall back to global settings
    return Settings::currencyLocalization('default_language', 'en');
}

// Add localization helper
// app/Helpers/LocalizationHelper.php
class LocalizationHelper
{
    public static function translate(string $key, array $params = [], string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        return __($key, $params, $locale);
    }
    
    public static function formatErrorMessage(string $errorKey, array $params = []): string
    {
        $translations = [
            'validation.required' => 'This field is required.',
            'validation.email' => 'Please enter a valid email address.',
            'validation.numeric' => 'This field must be a number.',
            'validation.min.string' => 'This field must be at least :min characters.',
            'validation.max.file' => 'The file may not be greater than :max kilobytes.',
            'validation.image' => 'This must be an image file.',
            'validation.mimes' => 'This file must be a file of type: :values.'
        ];
        
        $message = $translations[$errorKey] ?? $errorKey;
        
        foreach ($params as $key => $value) {
            $message = str_replace(':' . $key, $value, $message);
        }
        
        return $message;
    }
}
```

#### 3. Number Formatting
**Problem Areas:**
- Numbers formatted for single locale
- Decimal separators not locale-aware
- Currency symbols not combined with number formatting

**Required Changes:**
```php
// Add number formatting service
// app/Services/NumberFormattingService.php
class NumberFormattingService
{
    private $locale;
    
    public function __construct()
    {
        $this->locale = app()->getLocale();
    }
    
    public function formatNumber(float $number, int $decimals = 2): string
    {
        return number_format(
            $number, 
            $decimals, 
            $this->getDecimalSeparator(), 
            $this->getThousandsSeparator()
        );
    }
    
    public function formatCurrency(float $amount, string $currency = null): string
    {
        $currency = $currency ?? Settings::currencyLocalization('primary_currency', 'NGN');
        $symbol = $this->getCurrencySymbol($currency);
        $formattedAmount = $this->formatNumber($amount);
        
        // Different placement based on locale
        if ($this->isSymbolBeforeAmount($currency)) {
            return $symbol . ' ' . $formattedAmount;
        } else {
            return $formattedAmount . ' ' . $symbol;
        }
    }
    
    private function getDecimalSeparator(): string
    {
        $separators = [
            'en' => '.',
            'es' => ',',
            'fr' => ',',
            'de' => ',',
            'it' => ',',
            'pt' => ','
        ];
        
        return $separators[$this->locale] ?? '.';
    }
    
    private function getThousandsSeparator(): string
    {
        $separators = [
            'en' => ',',
            'es' => '.',
            'fr' => ' ',
            'de' => '.',
            'it' => '.',
            'pt' => '.'
        ];
        
        return $separators[$this->locale] ?? ',';
    }
    
    private function isSymbolBeforeAmount(string $currency): bool
    {
        $beforeSymbols = ['USD', 'GBP', 'EUR', 'NGN'];
        return in_array($currency, $beforeSymbols);
    }
}
```

#### 4. Units of Measure
**Problem Areas:**
- Weight units hardcoded
- Volume measurements not localized
- Units not consistent across modules

**Required Changes:**
```php
// Add units service
// app/Services/UnitsOfMeasureService.php
class UnitsOfMeasureService
{
    private $locale;
    private $unitsSettings;
    
    public function __construct()
    {
        $this->locale = app()->getLocale();
        $this->unitsSettings = Settings::currencyLocalization('units_of_measure', []);
    }
    
    public function formatWeight(float $weight, string $unit = 'kg'): string
    {
        $weightUnit = $this->unitsSettings['weight_unit'] ?? 'kg';
        $displayUnit = $this->getLocalizedWeightUnit($weightUnit);
        
        return $this->formatNumber($weight) . ' ' . $displayUnit;
    }
    
    public function formatVolume(float $volume, string $unit = 'l'): string
    {
        $volumeUnit = $this->unitsSettings['volume_unit'] ?? 'l';
        $displayUnit = $this->getLocalizedVolumeUnit($volumeUnit);
        
        return $this->formatNumber($volume) . ' ' . $displayUnit;
    }
    
    public function formatQuantity(float $quantity): string
    {
        $quantityUnit = $this->unitsSettings['quantity_unit'] ?? 'pcs';
        $displayUnit = $this->getLocalizedQuantityUnit($quantityUnit);
        
        return $this->formatNumber($quantity) . ' ' . $displayUnit;
    }
    
    private function getLocalizedWeightUnit(string $unit): string
    {
        $localizedUnits = [
            'en' => ['kg' => 'kg', 'g' => 'g', 'lb' => 'lb', 'oz' => 'oz'],
            'es' => ['kg' => 'kg', 'g' => 'g', 'lb' => 'lb', 'oz' => 'oz'],
            'fr' => ['kg' => 'kg', 'g' => 'g', 'lb' => 'lb', 'oz' => 'oz'],
            'de' => ['kg' => 'kg', 'g' => 'g', 'lb' => 'lb', 'oz' => 'oz']
        ];
        
        return $localizedUnits[$this->locale][$unit] ?? $unit;
    }
    
    private function getLocalizedVolumeUnit(string $unit): string
    {
        $localizedUnits = [
            'en' => ['l' => 'L', 'ml' => 'mL', 'gal' => 'gal', 'qt' => 'qt'],
            'es' => ['l' => 'L', 'ml' => 'mL', 'gal' => 'gal', 'qt' => 'qt'],
            'fr' => ['l' => 'L', 'ml' => 'mL', 'gal' => 'gal', 'qt' => 'qt'],
            'de' => ['l' => 'L', 'ml' => 'mL', 'gal' => 'gal', 'qt' => 'qt']
        ];
        
        return $localizedUnits[$this->locale][$unit] ?? $unit;
    }
    
    private function getLocalizedQuantityUnit(string $unit): string
    {
        $localizedUnits = [
            'en' => ['pcs' => 'pcs', 'dozen' => 'doz', 'box' => 'box'],
            'es' => ['pcs' => 'pzas', 'dozen' => 'doc', 'box' => 'caja'],
            'fr' => ['pcs' => 'pcs', 'dozen' => 'douz', 'box' => 'boîte'],
            'de' => ['pcs' => 'Stk', 'dozen' => 'Dutz', 'box' => 'Kasten']
        ];
        
        return $localizedUnits[$this->locale][$unit] ?? $unit;
    }
    
    private function formatNumber(float $number): string
    {
        return number_format(
            $number, 
            2, 
            $this->getDecimalSeparator(), 
            $this->getThousandsSeparator()
        );
    }
}
```

### Implementation Priority
1. **Date Formatting** - High (user experience)
2. **Currency Formatting** - High (financial accuracy)
3. **Number Formatting** - Medium (display consistency)
4. **Language Localization** - Medium (accessibility)
5. **Units of Measure** - Low (specialized use)

### Files to Update
- All components that display dates
- All financial display components
- Inventory management components
- Production recipe components
- Report generation components

### Testing Requirements
- Test date format changes across all modules
- Verify currency formatting with different locales
- Test number formatting consistency
- Validate unit of measure conversions
- Test language switching functionality