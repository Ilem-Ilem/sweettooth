<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalReportsAnalytics;

class ReportsAnalytics extends Component
{
    public $reportsSales = true;
    public $reportsPurchases = true;
    public $reportsStock = true;
    public $reportsProfitLoss = true;
    public $customDateRange = true;
    public $multiSelectDelete = true;
    public $exportCsv = true;
    public $exportPdf = true;

    public function mount()
    {
        $settings = GlobalReportsAnalytics::first();
        
        if ($settings) {
            $reports = $settings->reports ?? [];
            $this->reportsSales = in_array('sales', $reports);
            $this->reportsPurchases = in_array('purchases', $reports);
            $this->reportsStock = in_array('stock', $reports);
            $this->reportsProfitLoss = in_array('pl', $reports);
            
            $this->customDateRange = $settings->custom_date_range === 'enabled';
            $this->multiSelectDelete = $settings->multi_select_delete === 'enabled';
            
            $export = $settings->export ?? [];
            $this->exportCsv = in_array('csv', $export);
            $this->exportPdf = in_array('pdf', $export);
        }
    }

    public function save()
    {
        $reports = [];
        if ($this->reportsSales) $reports[] = 'sales';
        if ($this->reportsPurchases) $reports[] = 'purchases';
        if ($this->reportsStock) $reports[] = 'stock';
        if ($this->reportsProfitLoss) $reports[] = 'pl';
        
        $export = [];
        if ($this->exportCsv) $export[] = 'csv';
        if ($this->exportPdf) $export[] = 'pdf';

        GlobalReportsAnalytics::updateOrCreate(
            ['id' => 1],
            [
                'reports' => $reports,
                'custom_date_range' => $this->customDateRange ? 'enabled' : 'disabled',
                'multi_select_delete' => $this->multiSelectDelete ? 'enabled' : 'disabled',
                'export' => $export,
            ]
        );

        session()->flash('message', 'Reports & Analytics settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.reports-analytics');
    }
}
