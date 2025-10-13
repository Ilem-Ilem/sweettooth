<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\Employee;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class EmployeeUsageAnalytics extends Component
{
    public $selectedEmployeeId = null;
    public $selectedEmployee = null;
    public $timeRange = '30'; // days

    public function getBranchId()
    {
        return request()->query('b_id');
    }

    public function mount()
    {
        $branchId = $this->getBranchId();

        // Select first employee by default
        $firstEmployee = Employee::whereHas('employee', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->first();

        if ($firstEmployee) {
            $this->selectedEmployeeId = $firstEmployee->id;
            $this->loadEmployeeData();
        }
    }

    public function updatedSelectedEmployeeId()
    {
        $this->loadEmployeeData();
    }

    public function loadEmployeeData()
    {
        if ($this->selectedEmployeeId) {
            $this->selectedEmployee = Employee::find($this->selectedEmployeeId);
        }
    }

    public function getEmployeeRequests()
    {
        if (!$this->selectedEmployeeId) {
            return collect([]);
        }

        $branchId = $this->getBranchId();
        $startDate = Carbon::now()->subDays($this->timeRange);

        return ItemRequest::with(['department', 'requestDetails.item'])
            ->where('requested_by', $this->selectedEmployeeId)
            ->where('branch_id', $branchId)
            ->where('request_date', '>=', $startDate)
            ->orderBy('request_date', 'desc')
            ->get();
    }

    public function getEmployeeSummary()
    {
        if (!$this->selectedEmployeeId) {
            return null;
        }

        $branchId = $this->getBranchId();
        $startDate = Carbon::now()->subDays($this->timeRange);

        $requests = ItemRequest::where('requested_by', $this->selectedEmployeeId)
            ->where('branch_id', $branchId)
            ->where('request_date', '>=', $startDate);

        return (object)[
            'total_requests' => $requests->count(),
            'approved' => $requests->clone()->where('status', 'approved')->count(),
            'pending' => $requests->clone()->where('status', 'pending')->count(),
            'rejected' => $requests->clone()->where('status', 'rejected')->count(),
            'completed' => $requests->clone()->where('status', 'completed')->count(),
        ];
    }

    public function getMostRequestedItems()
    {
        if (!$this->selectedEmployeeId) {
            return collect([]);
        }

        $branchId = $this->getBranchId();
        $startDate = Carbon::now()->subDays($this->timeRange);

        return ItemRequestDetail::select('items.name', 'items.sku', DB::raw('SUM(item_request_details.quantity_requested) as total_quantity'), DB::raw('COUNT(item_request_details.id) as request_count'))
            ->join('item_requests', 'item_request_details.request_id', '=', 'item_requests.id')
            ->join('items', 'item_request_details.item_id', '=', 'items.id')
            ->where('item_requests.requested_by', $this->selectedEmployeeId)
            ->where('item_requests.branch_id', $branchId)
            ->where('item_requests.request_date', '>=', $startDate)
            ->groupBy('items.id', 'items.name', 'items.sku')
            ->orderByDesc('request_count')
            ->limit(10)
            ->get();
    }

    public function getRequestTrends()
    {
        if (!$this->selectedEmployeeId) {
            return [];
        }

        $branchId = $this->getBranchId();
        $startDate = Carbon::now()->subDays($this->timeRange);

        $requests = ItemRequest::selectRaw('DATE(request_date) as date, COUNT(*) as count')
            ->where('requested_by', $this->selectedEmployeeId)
            ->where('branch_id', $branchId)
            ->where('request_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return $requests->map(function($item) {
            return [
                'date' => Carbon::parse($item->date)->format('M d'),
                'count' => $item->count
            ];
        })->toArray();
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        $employees = Employee::whereHas('employee', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->orderBy('name')->get();

        $employeeRequests = $this->getEmployeeRequests();
        $employeeSummary = $this->getEmployeeSummary();
        $mostRequestedItems = $this->getMostRequestedItems();
        $requestTrends = $this->getRequestTrends();

        return view('livewire.branch-dashboard.analytics.employee-usage-analytics', [
            'employees' => $employees,
            'employeeRequests' => $employeeRequests,
            'employeeSummary' => $employeeSummary,
            'mostRequestedItems' => $mostRequestedItems,
            'requestTrends' => $requestTrends,
        ]);
    }
}
