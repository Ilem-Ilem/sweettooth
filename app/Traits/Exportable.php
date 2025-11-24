<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\ExportExcelJob;
use App\Jobs\ExportPdfJob;

trait Exportable
{
    /**
     * Universal export method for PDF & Excel with advanced formatting
     *
     * @param string $filename          Base filename (without extension)
     * @param Collection|array $data    Data to export
     * @param string|array $views       Blade view path(s) - can be ['pdf' => 'path', 'excel' => 'path']
     * @param string $format            'pdf', 'excel', or 'both' (default: 'both')
     * @param bool $queue               Set true for large datasets (>500 rows)
     * @param array $exportOptions      Configure: format, paper, orientation, margins, styles, etc.
     * @return mixed
     */
    public function export(
        string $filename,
        $data,
        $views,
        string $format = 'both',
        bool $queue = false,
        array $exportOptions = []
    ) {
        $data = collect($data);

        if ($data->isEmpty()) {
            session()->flash('error', 'No data to export.');
            return redirect()->back();
        }

        // Normalize views to array format
        $viewsArray = $this->normalizeViews($views);
        
        // Merge default options with custom options
        $options = array_merge($this->getDefaultExportOptions(), $exportOptions);

        // Determine if we should queue
        $shouldQueue = $queue && ($data->count() > ($options['queue_threshold'] ?? 500));

        if ($shouldQueue) {
            return $this->queueExports($filename, $data, $viewsArray, $format, $options);
        }

        return $this->performImmediateExport($filename, $data, $viewsArray, $format, $options);
    }

    /**
     * Normalize view paths to standardized array format
     */
    private function normalizeViews($views): array
    {
        if (is_string($views)) {
            return [
                'pdf' => $views,
                'excel' => $views,
            ];
        }

        if (is_array($views)) {
            return [
                'pdf' => $views['pdf'] ?? $views[0] ?? null,
                'excel' => $views['excel'] ?? $views[1] ?? null,
            ];
        }

        throw new \InvalidArgumentException('Views must be string or array');
    }

    /**
     * Get default export configuration options
     */
    private function getDefaultExportOptions(): array
    {
        return [
            // PDF Options
            'paper' => 'A4',
            'orientation' => 'portrait',
            'margins' => [
                'top' => 10,
                'right' => 10,
                'bottom' => 10,
                'left' => 10,
            ],
            'font_family' => 'DejaVu Sans',
            'font_size' => 9,
            
            // Excel Options
            'excel_columns_width' => 'auto',
            'excel_freeze_panes' => true,
            'excel_autofilter' => true,
            'excel_sheet_name' => 'Export',
            
            // General Options
            'queue_threshold' => 500,
            'use_memory_limit' => true,
            'memory_limit' => '512M',
            'include_timestamp' => false,
            'timezone' => config('app.timezone', 'UTC'),
        ];
    }

    /**
     * Queue export jobs for large datasets
     */
    private function queueExports(
        string $filename,
        Collection $data,
        array $views,
        string $format,
        array $options
    ) {
        if (in_array($format, ['pdf', 'both']) && $views['pdf']) {
            ExportPdfJob::dispatch(
                auth()->user()?->id,
                $filename,
                $data->toArray(),
                $views['pdf'],
                $options
            );
        }

        if (in_array($format, ['excel', 'both']) && $views['excel']) {
            ExportExcelJob::dispatch(
                auth()->user()?->id,
                $filename,
                $data->toArray(),
                $views['excel'],
                $options
            );
        }

        session()->flash('success', 'Export queued. You will be notified when ready.');
        return redirect()->back();
    }

    /**
     * Perform immediate export for small datasets
     */
    private function performImmediateExport(
        string $filename,
        Collection $data,
        array $views,
        string $format,
        array $options
    ) {
        // Prepare filename with optional timestamp
        $finalFilename = $this->formatFilename($filename, $options['include_timestamp'] ?? false);

        // PDF takes priority if both requested (browsers handle one download)
        if (in_array($format, ['pdf', 'both']) && $views['pdf']) {
            return $this->generatePdf($finalFilename, $data, $views['pdf'], $options);
        }

        if (in_array($format, ['excel', 'both']) && $views['excel']) {
            return $this->generateExcel($finalFilename, $data, $views['excel'], $options);
        }

        session()->flash('error', 'No valid export view configured.');
        return redirect()->back();
    }

    /**
     * Generate professional Excel export with formatting
     */
    private function generateExcel(
        string $filename,
        Collection $data,
        string $view,
        array $options
    ) {
        $excelClass = new class($data, $view, $options) 
            implements \Maatwebsite\Excel\Concerns\FromView,
                       \Maatwebsite\Excel\Concerns\WithStyles,
                       \Maatwebsite\Excel\Concerns\WithColumnWidths,
                       \Maatwebsite\Excel\Concerns\WithHeadings
        {
            protected $data;
            protected $view;
            protected $options;

            public function __construct($data, $view, $options)
            {
                $this->data = $data;
                $this->view = $view;
                $this->options = $options;
            }

            public function view(): \Illuminate\Contracts\View\View
            {
                return view($this->view, [
                    'data' => $this->data,
                    'forExcel' => true,
                    'options' => $this->options,
                ]);
            }

            public function styles($sheet)
            {
                return [
                    // Header row styling
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']],
                        'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                    ],
                    // Alternate row colors
                    'A2:Z1000' => [
                        'alignment' => ['vertical' => 'center', 'wrapText' => true],
                    ],
                ];
            }

            public function columnWidths(): array
            {
                // Auto-calculate widths based on content
                if ($this->options['excel_columns_width'] === 'auto') {
                    return [];  // Let Excel calculate
                }

                return $this->options['excel_columns_width'] ?? [];
            }

            public function headings(): array
            {
                // This works with data that has headers
                return [];
            }
        };

        $excel = Excel::download($excelClass, $filename . '.xlsx');

        // Auto-filter and freeze panes
        if ($this->options['excel_freeze_panes'] ?? true) {
            $excel->freezePane('A2');
        }

        return $excel;
    }

    /**
     * Generate professional PDF export with advanced formatting
     */
    private function generatePdf(
        string $filename,
        Collection $data,
        string $view,
        array $options
    ) {
        $pdf = Pdf::loadView($view, [
            'data' => $data,
            'forPdf' => true,
            'options' => $options,
        ]);

        // Set paper size
        $paper = $options['paper'] ?? 'A4';
        $orientation = $options['orientation'] ?? 'portrait';
        $pdf->setPaper($paper, $orientation);

        // Set margins (in millimeters)
        $margins = $options['margins'] ?? [];
        if (!empty($margins)) {
            $pdf->setOption('margin-top', $margins['top'] ?? 10)
                ->setOption('margin-right', $margins['right'] ?? 10)
                ->setOption('margin-bottom', $margins['bottom'] ?? 10)
                ->setOption('margin-left', $margins['left'] ?? 10);
        }

        // Additional PDF options
        if (!empty($options['dpi'])) {
            $pdf->setOption('dpi', $options['dpi']);
        }

        if ($options['enable_remote'] ?? false) {
            $pdf->setOption('enable_remote', true);
        }

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Format filename with optional timestamp
     */
    private function formatFilename(string $filename, bool $includeTimestamp = false): string
    {
        $filename = str_replace([' ', '/'], '_', $filename);
        
        if ($includeTimestamp) {
            $timestamp = now()->format('Y_m_d_H_i_s');
            return "{$filename}_{$timestamp}";
        }

        return $filename;
    }

    /**
     * Quick export method - shorthand for common use cases
     *
     * Usage: $model->quickExport('users', $data, 'exports.users')
     */
    public function quickExport(
        string $filename,
        $data,
        string $view,
        string $format = 'both'
    ) {
        return $this->export($filename, $data, $view, $format);
    }

    /**
     * Export with custom styling for professional output
     *
     * Usage: $model->styledExport('report', $data, [
     *     'views' => ['pdf' => 'exports.pdf.report', 'excel' => 'exports.excel.report'],
     *     'paper' => 'A4',
     *     'orientation' => 'landscape',
     * ])
     */
    public function styledExport(
        string $filename,
        $data,
        array $config = []
    ) {
        $views = $config['views'] ?? $config['view'] ?? null;
        $format = $config['format'] ?? 'both';
        $queue = $config['queue'] ?? false;
        $options = array_diff_key($config, ['views' => null, 'view' => null, 'format' => null, 'queue' => null]);

        if (!$views) {
            throw new \InvalidArgumentException('View(s) must be provided in config');
        }

        return $this->export($filename, $data, $views, $format, $queue, $options);
    }

    /**
     * Batch export multiple datasets
     *
     * Usage: $model->batchExport([
     *     ['filename' => 'users', 'data' => $users, 'view' => 'exports.users'],
     *     ['filename' => 'products', 'data' => $products, 'view' => 'exports.products'],
     * ])
     */
    public function batchExport(array $exports, string $format = 'both', array $options = [])
    {
        $results = [];

        foreach ($exports as $export) {
            $results[] = $this->export(
                $export['filename'],
                $export['data'],
                $export['view'],
                $format,
                $export['queue'] ?? false,
                array_merge($options, $export['options'] ?? [])
            );
        }

        return $results;
    }

    /**
     * Export collection data with mapping/transformation
     *
     * Usage: $model->mapAndExport('users', $data, 'exports.users', [
     *     'name' => fn($user) => $user->first_name . ' ' . $user->last_name,
     *     'email' => fn($user) => strtolower($user->email),
     * ])
     */
    public function mapAndExport(
        string $filename,
        $data,
        string $view,
        array $mapping,
        string $format = 'both'
    ) {
        $mappedData = collect($data)->map(function ($item) use ($mapping) {
            $row = [];
            foreach ($mapping as $key => $transformer) {
                $row[$key] = is_callable($transformer) 
                    ? $transformer($item)
                    : $item[$transformer] ?? $item->{$transformer} ?? null;
            }
            return $row;
        });

        return $this->export($filename, $mappedData, $view, $format);
    }
}
