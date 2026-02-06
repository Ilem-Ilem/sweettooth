<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\HealthCheck;
use App\Models\ItemRequest;
use App\Models\DepartmentReport;
use App\Exports\DepartmentReportExport;
use App\Traits\Exportable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use Exportable;

    /**
     * Export stock level analytics
     */
    public function stockLevelAnalytics(Request $request)
    {
        $branchId = Auth::guard('web')->user()?->branch_id ?? $request->get('b_id');
        
        $stocks = Stock::with(['item'])
            ->where('branch_id', $branchId)
            ->when($request->get('search'), function ($query) use ($request) {
                $query->whereHas('item', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->get('search') . '%')
                      ->orWhere('sku', 'like', '%' . $request->get('search') . '%');
                });
            })
            ->when($request->get('category'), function ($query) use ($request) {
                $query->whereHas('item', function ($q) use ($request) {
                    $q->where('category', $request->get('category'));
                });
            })
            ->when($request->get('health'), function ($query) use ($request) {
                $query->where('health_status', $request->get('health'));
            })
            ->get();

        if ($stocks->isEmpty()) {
            return back()->with('warning', 'No stock data to export.');
        }

        $format = $request->get('format', 'excel');
        
        if ($format === 'csv') {
            return $this->exportAsCSV($stocks);
        }
        
        return $this->export(
            'stock-level-analytics-' . now()->format('Y-m-d'),
            $stocks,
            'exports.analytics.stock-level-analytics',
            $format
        );
    }

    /**
     * Export stocks as CSV
     */
    private function exportAsCSV($stocks)
    {
        $filename = 'stock-level-analytics-' . now()->format('Y-m-d-His') . '.csv';
        
        return response()->streamDownload(function () use ($stocks) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Item', 'SKU', 'Category', 'Available', 'Reserved', 'Damaged', 'Reorder Level', 'Health Status']);

            foreach ($stocks as $stock) {
                fputcsv($handle, [
                    $stock->item->name,
                    $stock->item->sku,
                    str_replace('_', ' ', ucfirst($stock->item->category)),
                    number_format($stock->quantity_available, 2),
                    number_format($stock->quantity_reserved, 2),
                    number_format($stock->quantity_damaged, 2),
                    $stock->item->reorder_level ? number_format($stock->item->reorder_level, 2) : 'Not set',
                    ucfirst($stock->health_status),
                ]);
            }
            fclose($handle);
        }, $filename);
    }

    /**
     * Export health checks
     */
    public function healthChecks(Request $request)
    {
        $branchId = Auth::guard('web')->user()?->branch_id ?? $request->get('b_id');
        
        $healthChecks = HealthCheck::with(['stock.item', 'stock.branch', 'checker'])
            ->whereHas('stock', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->when($request->get('search'), function ($q) use ($request) {
                $q->whereHas('stock.item', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->get('search') . '%')
                        ->orWhere('sku', 'like', '%' . $request->get('search') . '%');
                });
            })
            ->when($request->get('condition'), fn($q) => $q->where('condition', $request->get('condition')))
            ->orderBy('check_date', 'desc')
            ->get();

        if ($healthChecks->isEmpty()) {
            return back()->with('warning', 'No health checks to export.');
        }

        $format = $request->get('format', 'excel');

        return $this->export(
            'health-checks-' . now()->format('Y-m-d'),
            $healthChecks,
            'exports.inventory.health-checks',
            $format
        );
    }

    /**
     * Export item requests
     */
    public function itemRequests(Request $request)
    {
        $branchId = Auth::guard('web')->user()?->branch_id ?? $request->get('b_id');
        
        $requests = ItemRequest::with(['branch', 'department', 'requester', 'requestDetails'])
            ->where('branch_id', $branchId)
            ->when($request->get('search'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('request_number', 'like', '%' . $request->get('search') . '%')
                        ->orWhereHas('requester', function ($subQuery) use ($request) {
                            $subQuery->where('name', 'like', '%' . $request->get('search') . '%');
                        })
                        ->orWhereHas('department', function ($subQuery) use ($request) {
                            $subQuery->where('name', 'like', '%' . $request->get('search') . '%');
                        });
                });
            })
            ->when($request->get('department'), fn($q) => $q->where('department_id', $request->get('department')))
            ->when($request->get('status'), fn($q) => $q->where('status', $request->get('status')))
            ->orderBy('created_at', 'desc')
            ->get();

        if ($requests->isEmpty()) {
            return back()->with('warning', 'No requests to export.');
        }

        $format = $request->get('format', 'excel');

        return $this->export(
            'item-requests-' . now()->format('Y-m-d'),
            $requests,
            'exports.inventory.item-requests',
            $format
        );
    }

    /**
     * Export a saved department report as Excel.
     */
    public function departmentReport(Request $request, string $reportId)
    {
        $user = Auth::guard('web')->user();
        if (! $user || ! $this->canExportReports($user)) {
            abort(403, 'You do not have permission to export reports.');
        }

        $branchId = $user->branch_id ?? $request->get('b_id') ?? current_branch_id();
        $report = DepartmentReport::query()
            ->with('department')
            ->where('branch_id', $branchId)
            ->findOrFail($reportId);

        $filename = sprintf(
            'report-%s-%s-%s',
            $report->report_category,
            $report->report_type,
            now()->format('Y-m-d')
        );

        return Excel::download(new DepartmentReportExport($report), $filename . '.xlsx');
    }

    private function canExportReports($user): bool
    {
        $checks = [
            'export-reports',
            'export_reports',
            'export-inventory-reports',
            'export_inventory_reports',
        ];

        foreach ($checks as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }
}
