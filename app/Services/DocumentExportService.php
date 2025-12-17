<?php

namespace App\Services;

use App\Helpers\Settings;
use App\Models\Receipt;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentExportService
{
    protected CurrencyFormattingService $currencyService;
    protected PosDocumentService $documentService;

    public function __construct()
    {
        $this->currencyService = new CurrencyFormattingService();
        $this->documentService = new PosDocumentService();
    }

    /**
     * Export receipt as PDF
     */
    public function exportReceiptAsPdf(Receipt $receipt): string
    {
        $businessName = Settings::businessConfiguration('business_name', config('app.name'));
        $receiptHtml = $this->documentService->generateBrandedReceiptHtml($receipt->sale);
        $currency = Settings::currencyLocalization('primary_currency', 'NGN');
        $currencySymbol = $this->currencyService->getSymbol($currency);

        $pdf = Pdf::loadView('exports.receipt-pdf', [
            'receipt' => $receipt,
            'receiptHtml' => $receiptHtml,
            'businessName' => $businessName,
            'currencySymbol' => $currencySymbol,
            'currencyService' => $this->currencyService,
        ]);

        return $pdf->download("Receipt-{$receipt->receipt_number}.pdf")->getContent();
    }

    /**
     * Export invoice as PDF
     */
    public function exportInvoiceAsPdf(Sale $sale): string
    {
        $businessName = Settings::businessConfiguration('business_name', config('app.name'));
        $invoiceHtml = $this->documentService->generateBrandedInvoiceHtml($sale);
        $currency = Settings::currencyLocalization('primary_currency', 'NGN');
        $currencySymbol = $this->currencyService->getSymbol($currency);

        $pdf = Pdf::loadView('exports.invoice-pdf', [
            'sale' => $sale,
            'invoiceHtml' => $invoiceHtml,
            'businessName' => $businessName,
            'currencySymbol' => $currencySymbol,
            'currencyService' => $this->currencyService,
        ]);

        return $pdf->download("Invoice-{$sale->receipt_number}.pdf")->getContent();
    }

    /**
     * Export multiple receipts as CSV
     */
    public function exportReceiptsAsCsv(array $receiptIds): string
    {
        $receipts = Receipt::whereIn('id', $receiptIds)->get();
        $csv = "Receipt #,Date,Subtotal,Tax,Discount,Total,Change Due\n";

        foreach ($receipts as $receipt) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $receipt->receipt_number,
                $receipt->created_at->format('Y-m-d H:i:s'),
                $this->currencyService->format($receipt->subtotal),
                $this->currencyService->format($receipt->tax),
                $this->currencyService->format($receipt->discount),
                $this->currencyService->format($receipt->total),
                $this->currencyService->format($receipt->change_due)
            );
        }

        return $csv;
    }

    /**
     * Export sales summary for a period
     */
    public function exportSalesSummaryAsCsv(\DateTime $startDate, \DateTime $endDate, ?int $branchId = null): string
    {
        $query = Sale::whereBetween('created_at', [$startDate, $endDate]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $sales = $query->with('receipts')->get();
        $currency = Settings::currencyLocalization('primary_currency', 'NGN');
        $symbol = $this->currencyService->getSymbol($currency);

        $csv = "Receipt #,Date,Order Type,Subtotal,Tax,Discount,Total,Payment Status\n";

        foreach ($sales as $sale) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $sale->receipt_number,
                $sale->created_at->format('Y-m-d H:i:s'),
                ucfirst(str_replace('-', ' ', $sale->order_type ?? 'dine-in')),
                $this->currencyService->format($sale->subtotal),
                $this->currencyService->format($sale->tax),
                $this->currencyService->format($sale->discount),
                $this->currencyService->format($sale->total),
                ucfirst(str_replace('_', ' ', $sale->payment_status ?? 'pending'))
            );
        }

        return $csv;
    }

    /**
     * Print receipt (returns HTML for printing)
     */
    public function printReceipt(Receipt $receipt): string
    {
        return $this->documentService->generateBrandedReceiptHtml($receipt->sale);
    }

    /**
     * Print invoice (returns HTML for printing)
     */
    public function printInvoice(Sale $sale): string
    {
        return $this->documentService->generateBrandedInvoiceHtml($sale);
    }

    /**
     * Generate receipt barcode data for printing
     */
    public function generateBarcodeData(Receipt $receipt): array
    {
        return [
            'receipt_number' => $receipt->receipt_number,
            'date' => $receipt->created_at->format('Y-m-d'),
            'total' => $receipt->total,
            'barcode_format' => 'code128', // can be changed to other formats
        ];
    }

    /**
     * Validate receipt printability
     */
    public function canPrintReceipt(Receipt $receipt): bool
    {
        return $receipt->sale !== null && !empty($receipt->content);
    }

    /**
     * Get receipt printing configuration
     */
    public function getPrintingConfig(): array
    {
        return [
            'paper_size' => Settings::businessConfiguration('receipt_paper_size', 'thermal'),
            'page_width' => Settings::businessConfiguration('receipt_width', '80mm'),
            'page_height' => Settings::businessConfiguration('receipt_height', 'auto'),
            'margins' => Settings::businessConfiguration('receipt_margins', '5mm'),
            'font_size' => Settings::businessConfiguration('receipt_font_size', '12px'),
        ];
    }
}
