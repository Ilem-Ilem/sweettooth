<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\ItemRequest;
use App\Models\ItemDispatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app.branch-dashboard')]
class RequestDispatchAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $statusFilter = '';
    public $departmentFilter = '';

    protected $queryString = ['dateFrom', 'dateTo', 'statusFilter', 'departmentFilter'];

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }

    public function getRequestTrendData()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $requests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('DATE(request_date) as date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        $dates = $requests->pluck('date')->unique()->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray();
        $groupedByDate = $requests->groupBy('date');

        return [
            'categories' => $dates,
            'series' => [
                ['name' => 'Pending', 'data' => collect($groupedByDate)->map(fn($items) => $items->where('status', 'pending')->sum('count'))->values()->toArray()],
                ['name' => 'Approved', 'data' => collect($groupedByDate)->map(fn($items) => $items->where('status', 'approved')->sum('count'))->values()->toArray()],
                ['name' => 'Completed', 'data' => collect($groupedByDate)->map(fn($items) => $items->where('status', 'completed')->sum('count'))->values()->toArray()],
            ],
        ];
    }

    public function getDepartmentAnalysis()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $departments = ItemRequest::with('department')
            ->where('branch_id', $branchId)
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('department_id, COUNT(*) as request_count')
            ->groupBy('department_id')
            ->orderByDesc('request_count')
            ->get();

        return [
            'labels' => $departments->map(fn($d) => $d->department->name ?? 'Unknown')->toArray(),
            'series' => $departments->pluck('request_count')->toArray(),
        ];
    }

    public function getFulfillmentRate()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $requests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $requests->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))->toArray(),
            'series' => $requests->pluck('count')->toArray(),
        ];
    }

    public function getSummary()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $requests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->get();

        $dispatches = ItemDispatch::where('branch_id', $branchId)
            ->whereBetween('dispatch_time', [$this->dateFrom, $this->dateTo])
            ->get();

        return [
            'total_requests' => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'completed' => $requests->where('status', 'completed')->count(),
            'total_dispatches' => $dispatches->count(),
            'fulfillment_rate' => $requests->count() > 0 ? ($requests->where('status', 'completed')->count() / $requests->count() * 100) : 0,
        ];
    }

    public function render()
    {
        $branchId = Auth::guard('employees')->user()->branch_id;

        $requests = ItemRequest::with(['department', 'requestedBy'])
            ->where('branch_id', $branchId)
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->whereBetween('request_date', [$this->dateFrom, $this->dateTo])
            ->latest('request_date')
            ->paginate(15);

        return view('livewire.branch-dashboard.analytics.request-dispatch-analytics', [
            'requests' => $requests,
            'summary' => $this->getSummary(),
            'trendData' => $this->getRequestTrendData(),
            'departmentAnalysis' => $this->getDepartmentAnalysis(),
            'fulfillmentRate' => $this->getFulfillmentRate(),
        ]);
    }
}
