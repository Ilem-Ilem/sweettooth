<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Services\NotificationRecipientService;
use App\Notifications\ApprovalRequestCreated;
use App\Models\Branch;

class ApprovalAuditRequest extends Model
{
    protected $fillable = [
        'branch_id',
        'requester_id',
        'requester_type',
        'approver_id',
        'approver_type',
        'action',
        'description',
        'payload',
        'status',
        'comment',
        'approved_at',
        'denied_at',
        'rejection_comment',
    ];
    protected $casts = ['payload' => 'array'];

    // Polymorphic requester
    public function requester()
    {
        return $this->morphTo('requester');
    }

    public function approver()
    {
        return $this->morphTo('approver');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public static function createPending($requester, string $action, $model)
    {
        $branchId = $model->branch_id ?? (function_exists('current_branch_id') ? current_branch_id() : null);
        $req = static::create([
            'branch_id'      => $branchId,
            'requester_type' => get_class($requester),
            'requester_id'   => $requester->id,
            'action'         => $action . ':' . $model->getKey(),
            'description'    => request('reason'),
            'payload'        => $model->toArray(),
            'status'         => 'pending',
        ]);

        $recipients = app(NotificationRecipientService::class)->usersForRoles(
            array_merge(
                config('notifications.roles.hr', []),
                config('notifications.roles.admin', [])
            ),
            $branchId
        );

        Notification::send($recipients, new ApprovalRequestCreated($req));

        return $req;
    }
}
