<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Base class for all dashboards
 * Provides common functionality for all role-based dashboards
 */
abstract class BaseDashboard extends Component
{
    /**
     * Current user
     */
    protected $user;

    /**
     * Current branch ID
     */
    protected ?string $branchId = null;

    /**
     * Current user's role
     */
    protected ?string $roleId = null;

    /**
     * Cache duration in minutes
     */
    protected int $cacheDuration = 60;

    /**
     * Date range for metrics
     */
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    /**
     * Initialize dashboard
     */
    public function mount()
    {
        $this->user = current_actor();
        $this->branchId = current_branch_id();
        $this->setDefaultDateRange();
    }

    /**
     * Set default date range to current month
     */
    protected function setDefaultDateRange(): void
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->toDateString();
        $this->dateTo = Carbon::now()->endOfMonth()->toDateString();
    }

    /**
     * Get current branch ID
     */
    public function getBranchId(): ?string
    {
        return $this->branchId ?? current_branch_id();
    }

    /**
     * Get current user
     */
    public function getUser()
    {
        return $this->user ?? current_actor();
    }

    /**
     * Get user's primary role
     */
    public function getUserRole()
    {
        if (!$this->user) {
            return null;
        }

        if (method_exists($this->user, 'roles')) {
            return $this->user->roles()->first();
        }

        return null;
    }

    /**
     * Get user's role name
     */
    public function getUserRoleName(): ?string
    {
        $role = $this->getUserRole();
        return $role?->name;
    }

    /**
     * Cache a value with dashboard-specific cache key
     */
    protected function cacheKey(string $key): string
    {
        return 'dashboard:' . class_basename($this) . ':' . $this->getBranchId() . ':' . $key;
    }

    /**
     * Get cached value or execute callback
     */
    protected function remember(string $key, callable $callback)
    {
        return Cache::remember(
            $this->cacheKey($key),
            now()->addMinutes($this->cacheDuration),
            $callback
        );
    }

    /**
     * Forget cache
     */
    protected function forget(string $key): void
    {
        Cache::forget($this->cacheKey($key));
    }

    /**
     * Forget all dashboard cache
     */
    protected function forgetAll(): void
    {
        Cache::flush();
    }

    /**
     * Get date range as Carbon dates
     */
    protected function getDateRange(): array
    {
        return [
            'from' => Carbon::parse($this->dateFrom)->startOfDay(),
            'to' => Carbon::parse($this->dateTo)->endOfDay(),
        ];
    }

    /**
     * Check if user has permission
     */
    protected function hasPermission(string $permission): bool
    {
        if (!$this->user) {
            return false;
        }

        if (method_exists($this->user, 'hasPermissionTo')) {
            return $this->user->hasPermissionTo($permission);
        }

        return false;
    }

    /**
     * Check if user has role
     */
    protected function hasRole(string $role): bool
    {
        if (!$this->user) {
            return false;
        }

        if (method_exists($this->user, 'hasRole')) {
            return $this->user->hasRole($role);
        }

        return false;
    }

    /**
     * Get user's role level (1-5)
     * Helps determine what features to show
     */
    protected function getRoleLevel(): int
    {
        $roleName = $this->getUserRoleName();

        return match ($roleName) {
            // Level 5: Executive
            'super_admin', 'managing_director' => 5,

            // Level 4: Management
            'admin', 'branch_admin', 'head_of_production', 'sales_manager', 'hr_manager', 'inventory_manager' => 4,

            // Level 3: Supervisor
            'chef', 'head_of_gelato', 'confectionaries_manager', 'till_supervisor', 'corner_store_manager' => 3,

            // Level 2: Officer
            'hr_officer', 'stock_controller', 'store_keeper' => 2,

            // Level 1: Staff
            'kitchen_staff', 'gelato_production_staff', 'confectionaries_production_staff', 'cashier', 'corner_store_staff', 'confectionaries_sales_staff' => 1,

            // Default
            default => 0,
        };
    }

    /**
     * Format currency
     */
    protected function formatCurrency(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }

    /**
     * Format percentage
     */
    protected function formatPercentage(float $value, int $decimals = 1): string
    {
        return number_format($value, $decimals) . '%';
    }

    /**
     * Refresh dashboard data
     */
    public function refresh(): void
    {
        $this->forgetAll();
        $this->dispatch('refresh');
    }

    /**
     * Handle error with user-friendly message
     */
    protected function handleError(string $operation, \Exception $e): void
    {
        \Log::error("Dashboard error in {$operation}: " . $e->getMessage(), [
            'user_id' => $this->user?->id,
            'branch_id' => $this->getBranchId(),
            'exception' => $e,
        ]);

        session()->flash('error', "Failed to load {$operation}. Please try again.");
    }
}
