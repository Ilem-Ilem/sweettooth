# 05_BUSINESS_CONFIG_SETTINGS_APPLICATION.md

## Business Configuration Settings Application

### Current Issues with Business Settings Usage

#### 1. Company Information Display
**Problem Areas:**
- Company name settings not applied consistently
- Logo not displayed across all modules
- Contact information not integrated system-wide

**Current Hardcoded Locations to Fix:**
```php
// POS receipts need company info integration
// app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php:647-655
private function generateReceiptHeader(): string
{
    $companyInfo = $this->getCompanyInfo();
    
    return "
        <div style='text-align: center; margin-bottom: 20px;'>
            {$companyInfo['logo_html']}
            <h2 style='margin: 5px 0;'>{$companyInfo['name']}</h2>
            <p style='margin: 2px 0;'>{$companyInfo['phone']}</p>
            <p style='margin: 2px 0;'>{$companyInfo['email']}</p>
            <p style='margin: 2px 0;'>VAT: {$companyInfo['vat_number']}</p>
        </div>
    ";
}

private function getCompanyInfo(): array
{
    $settings = BranchBusinessConfiguration::where('branch_id', current_branch_id())->first();
    
    return [
        'name' => $settings?->company_name ?? config('app.name'),
        'logo_html' => $this->generateCompanyLogo($settings?->logo_upload),
        'phone' => $settings?->contact_details['phone'] ?? '',
        'email' => $settings?->contact_details['email'] ?? '',
        'vat_number' => $settings?->contact_details['vat_number'] ?? ''
    ];
}

private function generateCompanyLogo($logoPath): string
{
    if (!$logoPath || !Storage::disk('public')->exists($logoPath)) {
        return '';
    }
    
    $logoUrl = Storage::disk('public')->url($logoPath);
    return "<img src='{$logoUrl}' style='max-width: 200px; max-height: 80px;' alt='Company Logo'>";
}
```

#### 2. Report Headers and Branding
**Problem Areas:**
- Sales reports don't use company branding
- Inventory reports lack company information
- Production reports not branded

**Required Changes:**
```php
// Base report class with company info
// app/Services/BaseReportService.php
protected function getReportHeader(): string
{
    $companyInfo = $this->getCompanyInformation();
    $date = now()->format($this->getDateFormat());
    
    return "
        <div style='text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px;'>
            {$companyInfo['logo_html']}
            <h1 style='margin: 10px 0;'>{$companyInfo['name']}</h1>
            <p style='margin: 5px 0;'>{$companyInfo['phone']} | {$companyInfo['email']}</p>
            <p style='margin: 5px 0;'>VAT: {$companyInfo['vat_number']}</p>
            <p style='margin: 10px 0; font-weight: bold;'>Report Date: {$date}</p>
        </div>
    ";
}

private function getCompanyInformation(): array
{
    $settings = Settings::businessConfiguration('*', []);
    
    return [
        'name' => $settings['company_name'] ?? config('app.name'),
        'logo_html' => $this->generateCompanyLogo($settings['logo_upload'] ?? null),
        'phone' => $settings['contact_details']['phone'] ?? '',
        'email' => $settings['contact_details']['email'] ?? '',
        'vat_number' => $settings['contact_details']['vat_number'] ?? ''
    ];
}

private function getDateFormat(): string
{
    return Settings::currencyLocalization('date_format', 'MM/DD/YYYY');
}
```

#### 3. Email Templates Integration
**Problem Areas:**
- Email notifications don't use company branding
- Invoice emails lack company information
- Order confirmation emails not branded

**Required Changes:**
```php
// Base email template with company info
// app/Services/BaseEmailService.php
protected function getEmailHeader(): string
{
    $companyInfo = $this->getCompanyInformation();
    
    return "
        <div style='text-align: center; background-color: #f8f9fa; padding: 20px; border-bottom: 3px solid #007bff;'>
            {$companyInfo['logo_html']}
            <h2 style='color: #333; margin: 10px 0;'>{$companyInfo['name']}</h2>
            <p style='color: #666; margin: 5px 0;'>
                {$companyInfo['phone']} | {$companyInfo['email']}
            </p>
        </div>
    ";
}

protected function getEmailFooter(): string
{
    $companyInfo = $this->getCompanyInformation();
    
    return "
        <div style='background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;'>
            <p style='margin: 5px 0; color: #666;'>
                &copy; " . date('Y') . " {$companyInfo['name']}. All rights reserved.
            </p>
            <p style='margin: 5px 0; color: #666;'>
                VAT: {$companyInfo['vat_number']} | Phone: {$companyInfo['phone']}
            </p>
            <p style='margin: 5px 0; color: #666; font-size: 12px;'>
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    ";
}
```

#### 4. Invoice and Document Generation
**Problem Areas:**
- Invoice headers not using company settings
- Purchase orders lack company branding
- Quotations not using company information

**Required Changes:**
```php
// Invoice generation with company info
// app/Services/InvoiceService.php
public function generateInvoice($sale): array
{
    $companyInfo = $this->getCompanyInformation();
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    return [
        'company' => $companyInfo,
        'currency' => $currency,
        'invoice_number' => $sale->invoice_number,
        'date' => $sale->created_at->format($this->getDateFormat()),
        'customer' => $this->getCustomerInfo($sale),
        'items' => $this->getInvoiceItems($sale),
        'totals' => $this->calculateInvoiceTotals($sale),
        'payment_terms' => $this->getPaymentTerms()
    ];
}

// Purchase order with company branding
// app/Services/PurchaseOrderService.php
public function generatePurchaseOrder($purchase): array
{
    $companyInfo = $this->getCompanyInformation();
    
    return [
        'company' => $companyInfo,
        'po_number' => $purchase->po_number,
        'date' => $purchase->created_at->format($this->getDateFormat()),
        'supplier' => $this->getSupplierInfo($purchase),
        'items' => $this->getPOItems($purchase),
        'totals' => $this->calculatePOTotals($purchase),
        'delivery_terms' => $purchase->delivery_terms
    ];
}
```

### Implementation Priority
1. **POS Receipts** - High (customer-facing)
2. **Sales Reports** - High (business critical)
3. **Email Templates** - Medium (customer communication)
4. **Purchase Orders** - Medium (vendor communication)
5. **Internal Reports** - Low (internal use)

### Files to Update
- `app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php`
- `app/Services/BaseReportService.php`
- `app/Services/BaseEmailService.php`
- `app/Services/InvoiceService.php`
- `app/Services/PurchaseOrderService.php`
- All report generation components

### Testing Requirements
- Test POS receipt with company logo
- Verify email templates include company information
- Test document generation with company branding
- Validate contact information display accuracy