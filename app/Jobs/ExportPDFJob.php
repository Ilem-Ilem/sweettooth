<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\ExportReadyNotification;

class ExportPDFJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $filename;
    protected $data;
    protected $view;
    protected $options;

    public function __construct($userId, $filename, $data, $view, $options = [])
    {
        $this->userId = $userId;
        $this->filename = $filename;
        $this->data = $data;
        $this->view = $view;
        $this->options = $options;
    }

    public function handle()
    {
        $pdf = Pdf::loadView($this->view, ['data' => collect($this->data)]);

        if (!empty($this->options['paper'])) {
            $pdf->setPaper($this->options['paper'], $this->options['orientation'] ?? 'portrait');
        }

        $content = $pdf->output();
        $path = 'exports/' . $this->filename . '_' . now()->format('Y-m-d_His') . '.pdf';

        Storage::disk('public')->put($path, $content);

        $url = Storage::url($path);

        if ($this->userId) {
            $user = User::find($this->userId);
            $user?->notify(new ExportReadyNotification($url, 'PDF'));
        }
    }
}