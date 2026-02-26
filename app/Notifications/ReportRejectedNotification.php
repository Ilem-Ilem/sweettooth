<?php

namespace App\Notifications;

use App\Models\DepartmentReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public DepartmentReport $report;

    public function __construct(DepartmentReport $report)
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
            ->subject('Department Report Rejected')
            ->greeting("Hi {$notifiable->name},")
            ->line("Report \"{$this->report->report_name}\" was rejected.")
            ->action('View Report', route('branch-dashboard.reporting.report.view', ['id' => $this->report->id, 'b_id' => $this->report->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'report_rejected',
            'department_report_id' => $this->report->id,
            'branch_id' => $this->report->branch_id,
            'branch_name' => $this->report->branch?->name,
            'message' => "Report \"{$this->report->report_name}\" rejected.",
            'action_url' => route('branch-dashboard.reporting.report.view', ['id' => $this->report->id, 'b_id' => $this->report->branch_id]),
            'action_text' => 'View Report',
        ];
    }
}
