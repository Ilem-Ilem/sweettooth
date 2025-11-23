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
     * Universal export method for PDF & Excel with optional queueing
     *
     * @param string $filename      Base filename (without extension)
     * @param Collection|array $data   Data to export
     * @param string $view          Blade view path (e.g. 'exports.pdf.users')
     * @param string $format        'pdf' or 'excel' or 'both' (default: both)
     * @param bool $queue           Set true for large datasets
     * @param array $options        Extra options: paper, orientation, etc.
     */
    public function export(
        string $filename,
        $data,
        string $view,
        string $format = 'both',
        bool $queue = false,
        array $options = []
    ) {
        $data = collect($data);

        if ($data->isEmpty()) {
            session()->flash('error', 'No data to export.');
            return;
        }

        // Queue large exports
        if ($queue && ($data->count() > 500 || in_array($format, ['both', 'excel']))) {
            if (in_array($format, ['pdf', 'both'])) {
                ExportPdfJob::dispatch(auth()->user()?->id, $filename, $data->toArray(), $view, $options);
            }
            if (in_array($format, ['excel', 'both'])) {
                ExportExcelJob::dispatch(auth()->user()?->id, $filename, $data->toArray(), $view);
            }

            session()->flash('success', 'Export is being processed. You will be notified when ready.');
            return;
        }

        // Immediate export (small datasets)
        $responses = [];

        if (in_array($format, ['excel', 'both'])) {
            $responses[] = $this->generateExcel($filename, $data, $view);
        }

        if (in_array($format, ['pdf', 'both'])) {
            $responses[] = $this->generatePdf($filename, $data, $view, $options);
        }

        // Return first response (Livewire only handles one download at a time)
        return $responses ? $responses[0] : redirect()->back();
    }

    private function generateExcel($filename, Collection $data, string $view)
    {
        return Excel::download(
            new class($data, $view) implements \Maatwebsite\Excel\Concerns\FromView {
                protected $data;
                protected $view;

                public function __construct($data, $view)
                {
                    $this->data = $data;
                    $this->view = $view;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    return view($this->view, [
                        'data' => $this->data,
                        'forExcel' => true  // optional flag
                    ]);
                }
            },
            $filename . '.xlsx'
        );
    }

    private function generatePdf($filename, Collection $data, string $view, array $options = [])
    {
        $pdf = Pdf::loadView($view, [
            'data' => $data,
            'forPdf' => true
        ]);

        if (!empty($options['paper'])) && $pdf->setPaper($options['paper'], $options['orientation'] ?? 'portrait');

        return $pdf->download($filename . '.pdf');
    }
}