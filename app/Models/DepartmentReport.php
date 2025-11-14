<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentReport extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'department_id',
        'generated_by',
        'report_type',
        'report_category',
        'report_name',
        'report_date',
        'period_from',
        'period_to',
        'report_data',
        'summary_metrics',
        'charts_data',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'export_format',
        'file_path',
    ];

    protected $casts = [
        'report_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'report_data' => 'array',
        'summary_metrics' => 'array',
        'charts_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the branch that owns the report.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the department that owns the report.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employee who generated the report.
     */
    public function generatedBy()
    {
        return $this->belongsTo(Employee::class, 'generated_by');
    }

    /**
     * Get the employee who reviewed the report.
     */
    public function reviewedBy()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }

    /**
     * Get the distributions for this report.
     */
    public function distributions()
    {
        return $this->morphMany(ReportDistribution::class, 'reportable');
    }

    /**
     * Get the compiled reports that include this report.
     */
    public function compiledReports()
    {
        return $this->belongsToMany(CompiledReport::class, 'compiled_report_department_report')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include reports for a specific branch.
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to only include reports for a specific department.
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to filter by report category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('report_category', $category);
    }

    /**
     * Scope a query to filter by report type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('report_date', [$from, $to]);
    }

    /**
     * Mark report as reviewed.
     */
    public function markAsReviewed($employeeId, $notes = null)
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_by' => $employeeId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    /**
     * Mark report as compiled.
     */
    public function markAsCompiled()
    {
        $this->update(['status' => 'compiled']);
    }

    /**
     * Mark report as sent to MD.
     */
    public function markAsSentToMD()
    {
        $this->update(['status' => 'sent_to_md']);
    }

    /**
     * Check if report is editable.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'pending_review']);
    }

    /**
     * Check if report can be reviewed.
     */
    public function canBeReviewed(): bool
    {
        return $this->status === 'pending_review';
    }

    /**
     * Check if report can be compiled.
     */
    public function canBeCompiled(): bool
    {
        return in_array($this->status, ['reviewed', 'compiled']);
    }
}
