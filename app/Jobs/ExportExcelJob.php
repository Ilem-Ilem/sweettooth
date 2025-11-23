<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExportReadyNotification;

class ExportExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $userId;
    protected $filename;
    protected $data;
    protected $view;

    public function __construct($userId, $filename, $data, $view)
    {
        $this->userId = $userId;
        $this->filename = $filename;
        $this->data = $data;
        $this->view = $view;
    }

    public function handle()
    {
        $path = 'exports/' . $this->filename . '_' . now()->format('Y-m-d_His') . '.xlsx';

        Excel::store(
            new class($this->data, $this->view) implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\ShouldQueue {
                use \Maatwebsite\Excel\Concerns\Exportable;

                public function __construct($data, $view)
                {
                    $this->data = $data;
                    $this->view = $view;
                }

                public function view(): \Illuminate\Contracts\View\View
                {
                    return view($this->view, ['data' => collect($this->data)]);
                }
            },
            $path,
            'public'
        );

        $url = Storage::url($path);

        if ($this->userId) {
            {
            $user = User::find($this->userId);
            $user?->notify(new ExportReadyNotification($url, 'Excel'));
        }
    }
}