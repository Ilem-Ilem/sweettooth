<?php

namespace App\Livewire\BranchDashboard\ShiftManagement;

use App\Livewire\BaseComponent;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftConfiguration;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

#[Layout('components.layouts.app.branch-dashboard')]
class ShiftOverrides extends BaseComponent
{
    use Interactions, WithPagination;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $shifts;
    public $employees;
    public $shiftConfigurations;

    // Form fields for overrides
    public $shift_id;
    public $employee_id;
    public $shift_date;
    public $original_shift_type;
    public $new_shift_type;
    public $new_clock_in;
    public $new_clock_out;
    public $override_reason;
    public $override_status = 'pending'; // pending, approved, rejected

    public $showOverrideForm = false;
    public $showEditForm = false;

    public $search = '';
    public $selectedStatus = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        $this->b_id = current_branch_id();
        $this->dateFrom = today()->subDays(30)->format('Y-m-d');
        $this->dateTo = today()->format('Y-m-d');
        $this->loadData();
    }

    protected function getModelClass(): string
    {
        return Shift::class;
    }

    protected function getAllSelectableIds(): array
    {
        return [];
    }

    public function loadData()
    {
        $branchId = $this->b_id ?: current_branch_id();

        $query = Shift::query()
            ->where('branch_id', $branchId)
            ->with(['employee', 'configuration'])
            ->whereNotNull('metadata->config_id'); // Only shifts with configuration

        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('employee_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedStatus) {
            if ($this->selectedStatus === 'with_override') {
                $query->whereNotNull('metadata->override_reason');
            } elseif ($this->selectedStatus === 'without_override') {
                $query->whereNull('metadata->override_reason');
            }
        }

        if ($this->dateFrom) {
            $query->whereDate('shift_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('shift_date', '<=', $this->dateTo);
        }

        $this->shifts = $query->orderBy('shift_date', 'desc')
                              ->orderBy('clock_in', 'desc')
                              ->get();

        // Load employees and configurations
        $this->employees = Employee::where('branch_id', $branchId)->get();
        $this->shiftConfigurations = ShiftConfiguration::where('branch_id', $branchId)->get();
    }

    public function createOverride()
    {
        $this->resetForm();
        $this->showOverrideForm = true;
        $this->showEditForm = false;
    }

    public function storeOverride()
    {
        $this->validate([
            'shift_id' => 'required|exists:shifts,id',
            'override_reason' => 'required|string|max:500',
            'new_shift_type' => 'required|string|max:50',
            'new_clock_in' => 'required|date_format:H:i',
            'new_clock_out' => 'required|date_format:H:i|after:new_clock_in',
        ]);

        $shift = Shift::findOrFail($this->shift_id);

        // Update shift with override information
        $shift->update([
            'shift_type' => $this->new_shift_type,
            'clock_in' => Carbon::parse($shift->shift_date . ' ' . $this->new_clock_in),
            'clock_out' => Carbon::parse($shift->shift_date . ' ' . $this->new_clock_out),
            'status' => 'closed', // Override means shift is finalized
            'metadata' => array_merge($shift->metadata ?? [], [
                'override_reason' => $this->override_reason,
                'overridden_by' => auth()->id(),
                'overridden_at' => now(),
                'original_shift_type' => $shift->shift_type,
                'original_clock_in' => $shift->clock_in,
                'original_clock_out' => $shift->clock_out,
            ]),
        ]);

        $this->toast()->success('Shift override applied successfully!')->send();
        $this->resetForm();
        $this->showOverrideForm = false;
        $this->loadData();
    }

    public function edit($shiftId)
    {
        $shift = Shift::findOrFail($shiftId);

        $this->shift_id = $shift->id;
        $this->employee_id = $shift->employee_id;
        $this->shift_date = $shift->shift_date->format('Y-m-d');
        $this->original_shift_type = $shift->shift_type;
        $this->new_shift_type = $shift->shift_type;
        
        if ($shift->clock_in) {
            $this->new_clock_in = $shift->clock_in->format('H:i');
        }
        
        if ($shift->clock_out) {
            $this->new_clock_out = $shift->clock_out->format('H:i');
        }
        
        $this->override_reason = $shift->metadata['override_reason'] ?? '';
        $this->override_status = $shift->metadata['override_status'] ?? 'pending';

        $this->showEditForm = true;
        $this->showOverrideForm = false;
    }

    public function updateOverride()
    {
        $this->validate([
            'shift_id' => 'required|exists:shifts,id',
            'override_reason' => 'required|string|max:500',
            'new_shift_type' => 'required|string|max:50',
            'new_clock_out' => 'required|date_format:H:i|after:new_clock_in',
        ]);

        $shift = Shift::findOrFail($this->shift_id);

        // Update shift with override information
        $shift->update([
            'shift_type' => $this->new_shift_type,
            'clock_in' => Carbon::parse($shift->shift_date . ' ' . $this->new_clock_in),
            'clock_out' => Carbon::parse($shift->shift_date . ' ' . $this->new_clock_out),
            'status' => 'closed', // Override means shift is finalized
            'metadata' => array_merge($shift->metadata ?? [], [
                'override_reason' => $this->override_reason,
                'override_status' => $this->override_status,
                'overridden_by' => auth()->id(),
                'overridden_at' => now(),
                'original_shift_type' => $this->original_shift_type,
                'original_clock_in' => $shift->metadata['original_clock_in'] ?? $shift->clock_in,
                'original_clock_out' => $shift->metadata['original_clock_out'] ?? $shift->clock_out,
            ]),
        ]);

        $this->toast()->success('Shift override updated successfully!')->send();
        $this->resetForm();
        $this->showEditForm = false;
        $this->loadData();
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showOverrideForm = false;
        $this->showEditForm = false;
    }

    private function resetForm()
    {
        $this->shift_id = '';
        $this->employee_id = '';
        $this->shift_date = '';
        $this->original_shift_type = '';
        $this->new_shift_type = '';
        $this->new_clock_in = '';
        $this->new_clock_out = '';
        $this->override_reason = '';
        $this->override_status = 'pending';
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function updatedSelectedStatus()
    {
        $this->loadData();
    }

    public function updatedDateFrom()
    {
        $this->loadData();
    }

    public function updatedDateTo()
    {
        $this->loadData();
    }

    public function render()
    {
        $branch = current_branch();
        $this->loadData();

        return view('livewire.branch-dashboard.shift-management.shift-overrides', [
            'branch' => $branch,
        ]);
    }
}