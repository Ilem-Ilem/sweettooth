<?php

namespace App\Livewire\BranchDashboard\Analytics;

use App\Models\ItemRequest;
use App\Models\ItemDispatch;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('components.layouts.app.branch-dashboard')]
class RequestDispatchAnalytics extends Component
{
    use WithPagination;

    public $dateFrom;
    public $dateTo;
    public $statusFilter = '';
    public $departmentFilter = '';
    public $shiftFilter = '';
    public $searchTerm = '';

    protected $queryString = [
        'dateFrom',
        'dateTo',
        'statusFilter',
        'departmentFilter',
        'shiftFilter',
    ];

    public function mount()
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatedShiftFilter()
    {
        $this->resetPage();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Get request summary with extended metrics
     */
    public function getRequestSummary()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $requests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->get();

        return [
            'total_requests' => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
            'completed' => $requests->where('status', 'completed')->count(),
            'partially_fulfilled' => $requests->where('status', 'partially_fulfilled')->count(),
        ];
    }

    /**
     * Get dispatch summary
     */
    public function getDispatchSummary()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $dispatches = ItemDispatch::where('branch_id', $branchId)
            ->whereBetween('dispatch_time', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, function($q) {
                $q->whereHas('request', fn($query) => $query->where('department_id', $this->departmentFilter));
            })
            ->get();

        return [
            'total_dispatches' => $dispatches->count(),
            'completed' => $dispatches->where('status', 'completed')->count(),
            'pending' => $dispatches->where('status', 'pending')->count(),
            'failed' => $dispatches->where('status', 'failed')->count(),
            'total_quantity_dispatched' => $dispatches->sum('quantity_dispatched'),
        ];
    }

    /**
     * Get pending requests that need action
     */
    public function getPendingRequests()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return ItemRequest::with(['department', 'requestedBy', 'requestDetails.item'])
            ->where('branch_id', $branchId)
            ->where('status', 'pending')
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->orderBy('request_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($request) {
                $now = now();
                $requestDate = Carbon::parse($request->request_date);
                $waitingTime = $requestDate->diffInHours($now);

                return [
                    'request' => $request,
                    'waiting_time' => $waitingTime,
                    'urgency' => $waitingTime > 48 ? 'Critical' : ($waitingTime > 24 ? 'High' : 'Medium'),
                ];
            });
    }

    /**
     * Get most requested items
     */
    public function getMostRequestedItems()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return DB::table('item_request_details')
            ->join('item_requests', 'item_request_details.request_id', '=', 'item_requests.id')
            ->join('items', 'item_request_details.item_id', '=', 'items.id')
            ->leftJoin('units_of_measure', 'items.uom_id', '=', 'units_of_measure.id')
            ->where('item_requests.branch_id', $branchId)
            ->whereBetween('item_requests.request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('item_requests.department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('item_requests.shift', $this->shiftFilter))
            ->select(
                'items.id',
                'items.name',
                'items.sku',
                'units_of_measure.name as uom',
                DB::raw('COUNT(DISTINCT item_requests.id) as request_count'),
                DB::raw('SUM(item_request_details.quantity_requested) as total_quantity'),
                DB::raw('AVG(item_request_details.quantity_requested) as avg_quantity')
            )
            ->groupBy('items.id', 'items.name', 'items.sku', 'units_of_measure.name')
            ->orderByDesc('request_count')
            ->limit(10)
            ->get();
    }

    /**
     * Get department breakdown
     */
    public function getDepartmentBreakdown()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return ItemRequest::with('department')
            ->where('branch_id', $branchId)
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->select('department_id', 'status', DB::raw('COUNT(*) as count'))
            ->groupBy('department_id', 'status')
            ->get()
            ->groupBy('department_id')
            ->map(function ($group, $deptId) {
                $dept = Department::find($deptId);
                return [
                    'department' => $dept ? $dept->name : 'Unknown',
                    'total' => $group->sum('count'),
                    'pending' => $group->where('status', 'pending')->sum('count'),
                    'approved' => $group->where('status', 'approved')->sum('count'),
                    'rejected' => $group->where('status', 'rejected')->sum('count'),
                    'completed' => $group->where('status', 'completed')->sum('count'),
                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Get shift breakdown
     */
    public function getShiftBreakdown()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->select('shift', 'status', DB::raw('COUNT(*) as count'))
            ->groupBy('shift', 'status')
            ->get()
            ->groupBy('shift')
            ->map(function ($group, $shift) {
                return [
                    'shift' => ucfirst($shift),
                    'total' => $group->sum('count'),
                    'pending' => $group->where('status', 'pending')->sum('count'),
                    'approved' => $group->where('status', 'approved')->sum('count'),
                    'completed' => $group->where('status', 'completed')->sum('count'),
                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Get approval turnaround time
     */
    public function getApprovalTurnaroundTime()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $approvedRequests = ItemRequest::where('branch_id', $branchId)
            ->whereIn('status', ['approved', 'completed'])
            ->whereNotNull('approved_at')
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->select('id', 'request_number', 'request_date', 'approved_at')
            ->get()
            ->map(function ($request) {
                $requestDate = Carbon::parse($request->request_date);
                $approvedDate = Carbon::parse($request->approved_at);
                $hours = $requestDate->diffInHours($approvedDate);

                return [
                    'request_number' => $request->request_number,
                    'hours' => $hours,
                    'days' => round($hours / 24, 1),
                ];
            });

        $avgHours = $approvedRequests->avg('hours');

        return [
            'average_hours' => round($avgHours, 1),
            'average_days' => round($avgHours / 24, 1),
            'fastest' => $approvedRequests->sortBy('hours')->first(),
            'slowest' => $approvedRequests->sortByDesc('hours')->first(),
            'details' => $approvedRequests->sortByDesc('hours')->take(10),
        ];
    }

    /**
     * Get fulfillment rate
     */
    public function getFulfillmentRate()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        $requests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->get();

        $total = $requests->count();
        $completed = $requests->where('status', 'completed')->count();
        $rate = $total > 0 ? ($completed / $total) * 100 : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'rate' => round($rate, 1),
        ];
    }

    /**
     * Get daily trend
     */
    public function getDailyTrend()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        return ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->select(
                DB::raw('DATE(request_date) as date'),
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date', 'status')
            ->orderBy('date', 'desc')
            ->limit(84) // 14 days * 6 possible statuses
            ->get()
            ->groupBy('date')
            ->map(function ($group, $date) {
                return [
                    'date' => Carbon::parse($date)->format('M d'),
                    'total' => $group->sum('count'),
                    'pending' => $group->where('status', 'pending')->sum('count'),
                    'approved' => $group->where('status', 'approved')->sum('count'),
                    'completed' => $group->where('status', 'completed')->sum('count'),
                    'rejected' => $group->where('status', 'rejected')->sum('count'),
                ];
            })
            ->reverse()
            ->take(14)
            ->values();
    }

    /**
     * Get period comparison
     */
    public function getPeriodComparison()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();
        $daysDiff = $dateFrom->diffInDays($dateTo);

        // Current period
        $currentRequests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->count();

        // Previous period
        $previousRequests = ItemRequest::where('branch_id', $branchId)
            ->whereBetween('request_date', [
                $dateFrom->copy()->subDays($daysDiff)->startOfDay(),
                $dateFrom->copy()->subDay()->endOfDay()
            ])
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->count();

        $change = $previousRequests > 0 ? (($currentRequests - $previousRequests) / $previousRequests) * 100 : 0;

        return [
            'current' => $currentRequests,
            'previous' => $previousRequests,
            'change' => round($change, 1),
        ];
    }

    /**
     * Generate smart insights
     */
    public function getSmartInsights()
    {
        $summary = $this->getRequestSummary();
        $fulfillmentRate = $this->getFulfillmentRate();
        $insights = [];

        // Pending requests insight
        if ($summary['pending'] > 0) {
            $insights[] = [
                'type' => 'warning',
                'icon' => '⚠️',
                'message' => "You have {$summary['pending']} pending requests waiting for approval."
            ];
        }

        // High fulfillment rate
        if ($fulfillmentRate['rate'] >= 80) {
            $insights[] = [
                'type' => 'success',
                'icon' => '✅',
                'message' => "Great job! {$fulfillmentRate['rate']}% fulfillment rate shows excellent request processing."
            ];
        }

        // Low fulfillment rate
        if ($fulfillmentRate['rate'] < 50 && $summary['total_requests'] > 0) {
            $insights[] = [
                'type' => 'critical',
                'icon' => '🔴',
                'message' => "Fulfillment rate is low at {$fulfillmentRate['rate']}%. Review pending and rejected requests."
            ];
        }

        // Rejected requests
        if ($summary['rejected'] > 0) {
            $percentage = round(($summary['rejected'] / $summary['total_requests']) * 100, 1);
            $insights[] = [
                'type' => 'warning',
                'icon' => '❌',
                'message' => "{$summary['rejected']} requests ({$percentage}%) were rejected. Review rejection reasons."
            ];
        }

        return $insights;
    }

    public function render()
    {
        $branchId = @Auth::guard('web')->user()->branch_id ??  request()->get('b_id');
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

        // Main requests table with all filters applied
        $requests = ItemRequest::with(['department', 'requestedBy', 'approver', 'requestDetails'])
            ->where('branch_id', $branchId)
            ->whereBetween('request_date', [$dateFrom, $dateTo])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->departmentFilter, fn($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->shiftFilter, fn($q) => $q->where('shift', $this->shiftFilter))
            ->when($this->searchTerm, function($q) {
                $q->where(function($query) {
                    $query->where('request_number', 'like', '%' . $this->searchTerm . '%')
                        ->orWhereHas('requestedBy', function($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                        })
                        ->orWhereHas('department', function($subQuery) {
                            $subQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                        });
                });
            })
            ->latest('request_date')
            ->paginate(15);

        $departments = Department::orderBy('name')->get();
        $statuses = ['pending', 'approved', 'rejected', 'completed', 'partially_fulfilled'];
        $shifts = ['morning', 'afternoon', 'night'];

        return view('livewire.branch-dashboard.analytics.request-dispatch-analytics', [
            'requests' => $requests,
            'departments' => $departments,
            'statuses' => $statuses,
            'shifts' => $shifts,
            'requestSummary' => $this->getRequestSummary(),
            'dispatchSummary' => $this->getDispatchSummary(),
            'pendingRequests' => $this->getPendingRequests(),
            'mostRequestedItems' => $this->getMostRequestedItems(),
            'departmentBreakdown' => $this->getDepartmentBreakdown(),
            'shiftBreakdown' => $this->getShiftBreakdown(),
            'approvalTurnaround' => $this->getApprovalTurnaroundTime(),
            'fulfillmentRate' => $this->getFulfillmentRate(),
            'dailyTrend' => $this->getDailyTrend(),
            'periodComparison' => $this->getPeriodComparison(),
            'smartInsights' => $this->getSmartInsights(),
        ]);
    }
}
