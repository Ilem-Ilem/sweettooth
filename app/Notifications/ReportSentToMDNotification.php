<?php

namespace App\Notifications;

use App\Models\CompiledReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportSentToMDNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public CompiledReport $report;

    public function __construct(CompiledReport $report)
    {
        $this->report = $report;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Compiled Report Sent to MD')
            ->greeting("Hi {$notifiable->name},")
            ->line("Compiled report \"{$this->report->compilation_title}\" was sent to MD.")
            ->action('View Compiled Report', route('branch-dashboard.reporting.compiled.view', ['id' => $this->report->id, 'b_id' => $this->report->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'report_sent_to_md',
            'compiled_report_id' => $this->report->id,
            'branch_id' => $this->report->branch_id,
            'branch_name' => $this->report->branch?->name,
            'message' => "Compiled report \"{$this->report->compilation_title}\" sent to MD.",
            'action_url' => route('branch-dashboard.reporting.compiled.view', ['id' => $this->report->id, 'b_id' => $this->report->branch_id]),
            'action_text' => 'View Compiled Report',
        ];
    }
}
