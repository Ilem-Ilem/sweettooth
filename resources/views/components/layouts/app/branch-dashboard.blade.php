<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ branch_route('branch-dashboard.index') }}"
            class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="branch_route('branch-dashboard.index')"
                    :current="request()->routeIs('branch-dashboard.index')" wire:navigate>{{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>

            @php
                $currentUser = auth()->user();
                // Ensure currentUser is a User object, not a string
                if ($currentUser && is_string($currentUser)) {
                    $currentUser = null;
                }
                $isSuperAdmin = is_super_admin();
                $sidebarService = \App\Services\SidebarVisibilityService::class;

            @endphp

            {{-- ==================== ADMINISTRATION (SUPER ADMIN ONLY) ==================== --}}
            @if ($sidebarService::canSeeAdministration($currentUser) || $sidebarService::isSuperAdmin())
            <flux:navlist.group :heading="__('Administration')" icon="cog-6-tooth">
                <flux:navlist.item icon="shield-check" :href="branch_route('branch-dashboard.roles.index')"
                    :current="request()->routeIs('branch-dashboard.roles.*')" wire:navigate>
                    {{ __('Roles & Permissions') }}
                </flux:navlist.item>

                <flux:navlist.group :heading="__('Branch Management')" expandable
                    :expanded="request()->routeIs('branch-dashboard.branches.*')" class="grid">
                    <flux:navlist.item icon="building-office-2" :href="branch_route('branch-dashboard.branches.index')"
                        :current="request()->routeIs('branch-dashboard.branches.index')" wire:navigate>
                        {{ __('All Branches') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="building-office-2" :href="branch_route('branch-dashboard.branches.deleted')"
                        :current="request()->routeIs('branch-dashboard.branches.deleted')" wire:navigate>
                        {{ __('Deleted Branches') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.item icon="document-text" :href="branch_route('branch-dashboard.md-reports.dashboard')"
                    :current="request()->routeIs('branch-dashboard.md-reports.*')" wire:navigate>
                    {{ __('MD Reports') }}
                </flux:navlist.item>

                <flux:navlist.item icon="cog" :href="branch_route('branch-dashboard.settings.index')"
                    :current="request()->routeIs('branch-dashboard.settings.index')" wire:navigate>
                    {{ __('System Settings') }}
                </flux:navlist.item>

                <flux:navlist.item icon="cog" :href="branch_route('branch-dashboard.audit.index')"
                :current="request()->routeIs('branch-dashboard.audit.index')" wire:navigate>
                {{ __('Audit Management') }}
                </flux:navlist.item>
<!--                  branch-dashboard.audit.index  -->
            </flux:navlist.group>
            @endif
            {{-- ==================== END ADMINISTRATION ==================== --}}
            @if ($sidebarService::canSeeOrganization($currentUser) || $sidebarService::isSuperAdmin())
            <flux:navlist.group :heading="__('Organization')" class="grid">
                @if ($sidebarService::canSeeDepartments($currentUser))
                <flux:navlist.group :heading="__('Organization')" expandable
                    :expanded="request()->routeIs('branch-dashboard.branch.departments.*')" class="grid">
                    <flux:navlist.item icon="tag"
                        :href="branch_route('branch-dashboard.branch.departments.category')"
                        :current="request()->routeIs('branch-dashboard.branch.departments.category')" wire:navigate>
                        {{ __('Department Categories') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="building-storefront"
                        :href="branch_route('branch-dashboard.branch.departments.index')"
                        :current="request()->routeIs('branch-dashboard.branch.departments.index')" wire:navigate>
                        {{ __('Departments') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                @endif
                @if ($sidebarService::canSeeEmployeeManagement($currentUser) || $sidebarService::isSuperAdmin())
                <flux:navlist.group :heading="__('Employee Management')" expandable
                    :expanded="request()->routeIs('branch-dashboard.employees.*') || request()->routeIs('branch-dashboard.assignments.*') || request()->routeIs('branch-dashboard.clock-in-board.*')"
                    class="grid" icon='users'>
                    <flux:navlist.item icon="user" :href="branch_route('branch-dashboard.employee.index')"
                    :current="request()->routeIs('branch-dashboard.employee.index')" wire:navigate>
                        {{ __('All Employees') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="user-plus" :href="branch_route('branch-dashboard.employee.create')"
                        :current="request()->routeIs('branch-dashboard.employee.create')" wire:navigate>
                        {{ __('Create Employee') }}
                    </flux:navlist.item>

                    <flux:navlist.group :heading="__('Clock-In Board')" expandable
                        :expanded="request()->routeIs('branch-dashboard.clock-in-board.*')" class="grid" icon='clock'>
                        <flux:navlist.item icon="calendar-days" :href="branch_route('branch-dashboard.clock-in-board.today')"
                            :current="request()->routeIs('branch-dashboard.clock-in-board.today')" wire:navigate>
                            {{ __('Today') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="calendar" :href="branch_route('branch-dashboard.clock-in-board.all')"
                            :current="request()->routeIs('branch-dashboard.clock-in-board.all')" wire:navigate>
                            {{ __('All Employees (Date Range)') }}
                        </flux:navlist.item>
                    </flux:navlist.group>

                    {{--   @if ($sidebarService::canSeeRolesPermissions($currentUser))
                    <flux:navlist.item icon="user-plus" :href="branch_route('branch-dashboard.role-permission')"
                        :current="request()->routeIs('branch-dashboard.employee.role-permission')" wire:navigate>
                        {{ __('Roles') }}
                    </flux:navlist.item>
                    @endif
                    @if ($sidebarService::canSeeRoleAssignments($currentUser))
                     <flux:navlist.item icon="user-plus" :href="branch_route('branch-dashboard.role-assignments.index')"
                         :current="request()->routeIs('branch-dashboard.role-assignments.index')" wire:navigate>
                         {{ __('Assign Roles') }}
                     </flux:navlist.item>
                     @endif --}}

                     <flux:navlist.item icon="clipboard-document-list" :href="branch_route('branch-dashboard.employee-appraisals')"
                         :current="request()->routeIs('branch-dashboard.employee-appraisals')" wire:navigate>
                         {{ __('Employee Appraisals') }}
                     </flux:navlist.item>

                     <flux:navlist.item icon="calendar" :href="branch_route('branch-dashboard.hr.appraisals.cycles')"
                         :current="request()->routeIs('branch-dashboard.hr.appraisals.cycles')" wire:navigate>
                         {{ __('Appraisal Cycles') }}
                     </flux:navlist.item>


                    {{-- //role-assignments.index --}}
                </flux:navlist.group>
                @endif

                <flux:navlist.group :heading="__('Leave Management')" expandable
                    :expanded="request()->routeIs('branch-dashboard.leave.*')" class="grid">
                    @if (!$sidebarService::canSeeAdministration($currentUser))
                        <flux:navlist.item icon="calendar-days" :href="branch_route('branch-dashboard.leave.apply')"
                            :current="request()->routeIs('branch-dashboard.leave.apply')" wire:navigate>
                            {{ __('Apply Leave') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="clipboard-document-list"
                            :href="branch_route('branch-dashboard.leave.my-leaves')"
                            :current="request()->routeIs('branch-dashboard.leave.my-leaves')" wire:navigate>
                            {{ __('My Leaves') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="chart-pie" :href="branch_route('branch-dashboard.leave.balance')"
                            :current="request()->routeIs('branch-dashboard.leave.balance')" wire:navigate>
                            {{ __('Leave Balance') }}
                        </flux:navlist.item>
                    @endif
                    @if ($sidebarService::canSeeLeaveManagement($currentUser))
                        <flux:navlist.item icon="clipboard-document-check"
                            :href="branch_route('branch-dashboard.leave.approve')"
                            :current="request()->routeIs('branch-dashboard.leave.approve')" wire:navigate>
                            {{ __('Approve Leaves') }}
                        </flux:navlist.item>
                    @endif
                    @if ($sidebarService::canSeeEmployeeManagement($currentUser))
                        <flux:navlist.item icon="cog-6-tooth" :href="branch_route('branch-dashboard.leave.types')"
                            :current="request()->routeIs('branch-dashboard.leave.types')" wire:navigate>
                            {{ __('Leave Types') }}
                        </flux:navlist.item>
                    @endif


                </flux:navlist.group>



                 <flux:navlist.item icon="question-mark-circle" :href="branch_route('branch-dashboard.organization.helper')"
                     :current="request()->routeIs('branch-dashboard.organization.helper')" wire:navigate>
                     {{ __('Helper') }}
                 </flux:navlist.item>

             </flux:navlist.group>
            @endif


            @if ($sidebarService::canSeeInventoryDashboard($currentUser) || $sidebarService::isSuperAdmin())
            <flux:navlist.group :heading="__('Inventory')" icon='cube'>
                <flux:navlist.group :heading="__('Inventory Management')" expandable
                    :expanded="request()->routeIs('branch-dashboard.inventory.*')" class="grid" icon='cube'>
                    <flux:navlist.item icon="squares-2x2" :href="branch_route('branch-dashboard.inventory.items')"
                        :current="request()->routeIs('branch-dashboard.inventory.items')" wire:navigate>
                        {{ __('Items') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="truck" :href="branch_route('branch-dashboard.inventory.purchases')"
                        :current="request()->routeIs('branch-dashboard.inventory.purchases')" wire:navigate>
                        {{ __('Purchases') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="cube-transparent"
                        :href="branch_route('branch-dashboard.inventory.stocks')"
                        :current="request()->routeIs('branch-dashboard.inventory.stocks')" wire:navigate>
                        {{ __('Stock Levels') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="cube-transparent"
                        :href="branch_route('branch-dashboard.inventory.health-checks')"
                        :current="request()->routeIs('branch-dashboard.inventory.health-checks')" wire:navigate>
                        {{ __('Health Check') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list"
                        :href="branch_route('branch-dashboard.inventory.item-requests')"
                        :current="request()->routeIs('branch-dashboard.inventory.item-requests')" wire:navigate>
                        {{ __('Item Requests') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="truck"
                        :href="branch_route('branch-dashboard.inventory.item-dispatches')"
                        :current="request()->routeIs('branch-dashboard.inventory.item-dispatches')" wire:navigate>
                        {{ __('Dispatches') }}
                     </flux:navlist.item>

                     <flux:navlist.item icon="question-mark-circle"
                         :href="branch_route('branch-dashboard.inventory.helper')"
                         :current="request()->routeIs('branch-dashboard.inventory.helper')" wire:navigate>
                         {{ __('Helper') }}
                     </flux:navlist.item>

                 </flux:navlist.group>

                 <flux:navlist.group :heading="__('Callbacks')" class="grid" expandable
                     :expanded="request()->routeIs('branch-dashboard.inventory.callbacks.*')">
                    <flux:navlist.item icon="arrow-uturn-left"
                        :href="branch_route('branch-dashboard.inventory.callbacks.index')"
                        :current="request()->routeIs('branch-dashboard.inventory.callbacks.index')" wire:navigate>
                        {{ __('Production Callbacks') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist.group>
            @endif

            @if ($sidebarService::canSeeAnalytics($currentUser))
            <flux:navlist.group :heading="__('Analytics')" expandable
                :expanded="request()->routeIs('branch-dashboard.analytics.*')" class="grid" icon='chart-bar-square'>
                <flux:navlist.item icon="squares-plus" :href="branch_route('branch-dashboard.analytics.overview')"
                    :current="request()->routeIs('branch-dashboard.analytics.overview')">
                    {{ __('Overview Dashboard') }}
                </flux:navlist.item>
                <flux:navlist.item icon="cube" :href="branch_route('branch-dashboard.analytics.stock-level')"
                    :current="request()->routeIs('branch-dashboard.analytics.stock-level')" wire:navigate>
                    {{ __('Stock Levels') }}
                </flux:navlist.item>
                <flux:navlist.item icon="arrows-right-left"
                    :href="branch_route('branch-dashboard.analytics.stock-movement')"
                    :current="request()->routeIs('branch-dashboard.analytics.stock-movement')">
                    {{ __('Stock Movement') }}
                </flux:navlist.item>
                <flux:navlist.item icon="shopping-cart" :href="branch_route('branch-dashboard.analytics.purchase')"
                    :current="request()->routeIs('branch-dashboard.analytics.purchase')">
                    {{ __('Purchases') }}
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-list"
                    :href="branch_route('branch-dashboard.analytics.request-dispatch')"
                    :current="request()->routeIs('branch-dashboard.analytics.request-dispatch')">
                    {{ __('Requests & Dispatch') }}
                </flux:navlist.item>
                <flux:navlist.item icon="currency-dollar"
                    :href="branch_route('branch-dashboard.analytics.stock-valuation')"
                    :current="request()->routeIs('branch-dashboard.analytics.stock-valuation')">
                    {{ __('Stock Valuation') }}
                </flux:navlist.item>
                <flux:navlist.item icon="bell-alert" :href="branch_route('branch-dashboard.analytics.alerts')"
                    :current="request()->routeIs('branch-dashboard.analytics.alerts')">
                    {{ __('Alerts Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endif

             {{-- ==================== ACCOUNTING DASHBOARD ==================== --}}
            @if ($sidebarService::canSeeAccounting($currentUser))
            <flux:navlist.group :heading="__('Accounting')" icon="currency-dollar" expandable
                :expanded="request()->routeIs('branch-dashboard.accounting.*')" class="grid">
                <flux:navlist.item icon="chart-bar" :href="branch_route('branch-dashboard.accounting.dashboard')"
                    :current="request()->routeIs('branch-dashboard.accounting.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>

                <flux:navlist.item icon="eye" :href="branch_route('branch-dashboard.accounting.overview')"
                    :current="request()->routeIs('branch-dashboard.accounting.overview')" wire:navigate>
                    {{ __('Overview') }}
                </flux:navlist.item>
                
                @if ($currentUser->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'Admin', 'Accounting Manager']) || $sidebarService::isSuperAdmin())
                <flux:navlist.item icon="book-open" :href="branch_route('branch-dashboard.accounting.accounts')"
                    :current="request()->routeIs('branch-dashboard.accounting.accounts')" wire:navigate>
                    {{ __('Chart of Accounts') }}
                </flux:navlist.item>

                <flux:navlist.item icon="calendar" :href="branch_route('branch-dashboard.accounting.periods')"
                    :current="request()->routeIs('branch-dashboard.accounting.periods')" wire:navigate>
                    {{ __('Accounting Periods') }}
                </flux:navlist.item>
                @endif
                
                @if ($currentUser->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'Admin', 'Accountant', 'Accounting Manager'])  || $sidebarService::isSuperAdmin())
                <flux:navlist.item icon="document-plus" :href="branch_route('branch-dashboard.accounting.journal-entry')"
                    :current="request()->routeIs('branch-dashboard.accounting.journal-entry')" wire:navigate>
                    {{ __('Journal Entries') }}
                </flux:navlist.item>

                <flux:navlist.item icon="rectangle-stack" :href="branch_route('branch-dashboard.accounting.posting-status')"
                    :current="request()->routeIs('branch-dashboard.accounting.posting-status')" wire:navigate>
                    {{ __('Posting Status') }}
                </flux:navlist.item>

                <flux:navlist.item icon="currency-dollar" :href="branch_route('branch-dashboard.accounting.bank-reconciliation')"
                    :current="request()->routeIs('branch-dashboard.accounting.bank-reconciliation')" wire:navigate>
                    {{ __('Bank Reconciliation') }}
                </flux:navlist.item>
                @endif
                
                <flux:navlist.group :heading="__('Reports')" expandable
                    :expanded="request()->routeIs('branch-dashboard.accounting.reports.*')" class="grid">
                    <flux:navlist.item icon="list-bullet" :href="branch_route('branch-dashboard.accounting.reports.general-ledger')"
                        :current="request()->routeIs('branch-dashboard.accounting.reports.general-ledger')" wire:navigate>
                        {{ __('General Ledger') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="squares-2x2" :href="branch_route('branch-dashboard.accounting.reports.trial-balance')"
                        :current="request()->routeIs('branch-dashboard.accounting.reports.trial-balance')" wire:navigate>
                        {{ __('Trial Balance') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="chart-pie" :href="branch_route('branch-dashboard.accounting.reports.income-statement')"
                        :current="request()->routeIs('branch-dashboard.accounting.reports.income-statement')" wire:navigate>
                        {{ __('Income Statement') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="rectangle-group" :href="branch_route('branch-dashboard.accounting.reports.balance-sheet')"
                        :current="request()->routeIs('branch-dashboard.accounting.reports.balance-sheet')" wire:navigate>
                        {{ __('Balance Sheet') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="chart-bar-square" :href="branch_route('branch-dashboard.accounting.reports.cash-flow-statement')"
                        :current="request()->routeIs('branch-dashboard.accounting.reports.cash-flow-statement')" wire:navigate>
                        {{ __('Cash Flow') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist.group>
            @endif
            {{-- ==================== END ACCOUNTING DASHBOARD ==================== --}}

            {{-- ==================== DYNAMIC PRODUCTION MENU ==================== --}}
            @php
                $employee = \Illuminate\Support\Facades\Auth::user();
                // Safety check: ensure employee is a User object
                if ($employee && is_string($employee)) {
                    $employee = null;
                }
                // Reload to ensure department and roles relationships are loaded
                if ($employee && method_exists($employee, 'load')) {
                    $employee->load('department', 'roles');
                }
                $branchId = request()->get('b_id');
                $departments = collect();
                $OPEN_PRODUCTION = false;
                $OPEN_DEPT = null;

                // For Super Admin, always show production departments
                $forceShowProduction = $isSuperAdmin;

                // Get user's department for filtering
                $userDepartment = $employee?->department;

                // Production roles that can see all departments
                $adminProductionRoles = ['Head of Production'];

                // Department-specific production roles
                $departmentRestrictedRoles = [
                    'Chef',
                    'Kitchen Staff',
                    'Head of Gelato',
                    'Confectionaries Manager',
                    'Gelato Production Staff',
                    'Confectionaries Production Staff',
                ];

                $adminSalesRoles = ['Sales Manager'];

                // Department-specific sales roles
                $departmentRestrictedSalesRoles = [
                    'Till Supervisor',
                    'Cashier',
                    'Corner Store Manager',
                    'Corner Store Staff',
                    'Confectionaries Sales Staff',
                ];
            @endphp

            @if ($sidebarService::canSeeProduction($currentUser))
                @php
                if ($branchId || $forceShowProduction) {
                    $query = \App\Models\Department::with([
                        'category',
                        'pages' => fn($q) => $q->where('is_active', true)->orderBy('order')->orderBy('name'),
                    ]);

                    // For Super Admin, show all production departments across all branches
                    if ($forceShowProduction) {
                        $query->whereHas('category', fn($q) => $q->where('name', 'Production'));
                    } else {
                        // For regular users, filter by branch
                        $query->where(fn($q) => $q->where('branch_id', $branchId)->orWhereNull('branch_id'))
                              ->whereHas('category', fn($q) => $q->where('name', 'Production'));
                    }

                    $departments = $query->get();

                    // Filter departments based on user role
                    if (!$sidebarService::isProductionAdminRole($currentUser)) {
                        if ($userDepartment && $currentUser->hasAnyRole($departmentRestrictedRoles)) {
                            $departments = $departments->filter(fn($d) => $d->id === $userDepartment->id);
                        }
                    }

                    $departments = $departments->map(function ($dept) {
                        $dept->pages = $dept->pages->reject(
                            fn($p) => str_contains($p->route_name, 'edit') ||
                                str_contains($p->route_name, 'detail') ||
                                str_contains($p->route_name, 'shift-closing'),
                        );
                        return $dept;
                    });

                    $currentRoute = request()->route()?->getName();
                    $OPEN_DEPT = $departments->firstWhere(
                        fn($d) => $d->pages->pluck('route_name')->contains($currentRoute),
                    )?->id;

                    $nonDepartmentRoutes = [
                        'branch-dashboard.production.callbacks.index',
                        'branch-dashboard.production.callbacks.create-inventory',
                        'branch-dashboard.production.callbacks.approve-sales-callbacks',
                        'branch-dashboard.production.module.index',
                    ];
                    
                    $isProductionRoute = in_array($currentRoute, $nonDepartmentRoutes);

                    $OPEN_PRODUCTION = ($departments->isNotEmpty() || $OPEN_DEPT !== null || $isProductionRoute);
                }
            @endphp
            @endif

            @if ($OPEN_PRODUCTION)
            <flux:navlist.group :heading="__('Production')" icon="o-cog-6-tooth">
                @forelse($departments as $dept)
                    <flux:navlist.group :heading="$dept->name" :badge="$dept->category?->name" expandable
                        :expanded="(request()->get('dept_slug') == $dept->slug) ? true : false" class="grid">
                        @forelse($dept->pages as $page)
                            <flux:navlist.item icon="{{ $page->icon ?? 'o-beaker' }}"
                                :href="branch_route($page->route_name, [
                                    'deptSlug' => $dept->slug,
                                    'dept_slug' => $dept->slug,
                                    'page' => $page->name . '_' . $dept->slug
                                ])"
                                :current="request()->get('page') === $page->name . '_' . $dept->slug" wire:navigate>
                                {{ $page->name }}
                            </flux:navlist.item>
                        @empty
                            <div class="pl-10 pr-4 py-1.5 text-xs text-gray-500 italic">
                                {{ __('No pages configured') }}
                            </div>
                        @endforelse
                    </flux:navlist.group>
                @empty
                    <div class="pl-10 pr-4 py-1.5 text-xs text-gray-500 italic">
                        {{ __('No production departments') }}
                    </div>
                @endforelse
             </flux:navlist.group>
             @endif

             @if($sidebarService::canSeeProduction($currentUser))
             <flux:navlist.item icon="question-mark-circle"
                 :href="branch_route('branch-dashboard.production.helper')"
                 :current="request()->routeIs('branch-dashboard.production.helper')" wire:navigate>
                 {{ __('Production Helper') }}
             </flux:navlist.item>
             @endif

             @if($sidebarService::canSeeProductionCallbacks($currentUser))
            <flux:navlist.group :heading="__('Production Callbacks')" class="grid" expandable
                :expanded="request()->routeIs('branch-dashboard.production.callbacks.*')">
                <flux:navlist.item icon="arrow-uturn-left"
                    :href="branch_route('branch-dashboard.production.callbacks.index')"
                    :current="request()->routeIs('branch-dashboard.production.callbacks.index')" wire:navigate>
                    {{ __('Dispatch Callbacks') }}
                </flux:navlist.item>
                <flux:navlist.item icon="arrow-path-rounded-square"
                    :href="branch_route('branch-dashboard.production.callbacks.create-inventory')"
                    :current="request()->routeIs('branch-dashboard.production.callbacks.create-inventory')"
                    wire:navigate>
                    {{ __('Inventory Callbacks') }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endif
            {{-- ==================== END PRODUCTION MENU ==================== --}}

            {{-- ==================== SALES MENU (WITH DYNAMIC DEPARTMENTS) ==================== --}}
            @if($sidebarService::canSeeSalesManagement($currentUser))
            <flux:navlist.group :heading="__('Sales Management')" icon="shopping-cart">
                {{-- Static Sales Items --}}
                @if($sidebarService::canSeeSalesManagerItems($currentUser))
                <flux:navlist.item icon="clipboard-document-check"
                    :href="branch_route('branch-dashboard.sales-dashboard.stock-opening.index')"
                    :current="request()->routeIs('branch-dashboard.sales-dashboard.stock-opening.*')" wire:navigate>
                    {{ __('Stock Opening') }}
                </flux:navlist.item>

                <flux:navlist.item icon="truck"
                    :href="branch_route('branch-dashboard.sales-dashboard.dispatches.index')"
                    :current="request()->routeIs('branch-dashboard.sales-dashboard.dispatches.*')" wire:navigate>
                    {{ __('Kitchen Dispatches') }}
                </flux:navlist.item>

                <flux:navlist.item icon="clipboard-document-check"
                    :href="branch_route('branch-dashboard.sales-dashboard.stock-monitor')"
                    :current="request()->routeIs('branch-dashboard.sales-dashboard.stock-monitor')" wire:navigate>
                    {{ __('Monitor Product Stock') }}
                </flux:navlist.item>
                @endif

                <flux:navlist.item icon="chart-bar"
                    :href="branch_route('branch-dashboard.sales-dashboard.my-sales.index')"
                    :current="request()->routeIs('branch-dashboard.sales-dashboard.my-sales.*')" wire:navigate>
                    {{ __('My Sales Dashboard') }}
                </flux:navlist.item>



                <flux:navlist.item icon="question-mark-circle"
                    :href="branch_route('branch-dashboard.sales-dashboard.helper')"
                    :current="request()->routeIs('branch-dashboard.sales-dashboard.helper')" wire:navigate>
                    {{ __('Helper') }}
                </flux:navlist.item>

                <flux:navlist.group :heading="__('Callbacks')" class="grid" expandable
                    :expanded="request()->routeIs('branch-dashboard.sales-dashboard.callbacks.*')">
                    <flux:navlist.item icon="arrow-uturn-left"
                        :href="branch_route('branch-dashboard.sales-dashboard.callbacks.index')"
                        :current="request()->routeIs('branch-dashboard.sales-dashboard.callbacks.index')"
                        wire:navigate>
                        {{ __('Product Callbacks') }}
                    </flux:navlist.item>
                    <flux:navlist.item icon="arrow-path-rounded-square"
                        :href="branch_route('branch-dashboard.sales-dashboard.callbacks.dispatch-callbacks')"
                        :current="request()->routeIs('branch-dashboard.sales-dashboard.callbacks.dispatch-callbacks')"
                        wire:navigate>
                        {{ __('Dispatch Callbacks') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist.group>
            @endif

            {{-- ==================== DYNAMIC SALES DEPARTMENTS (POS) ==================== --}}
            @php
                $salesDepartments = collect();
                $OPEN_SALES = false;
                $OPEN_SALES_DEPT = null;

                if (($branchId || $isSuperAdmin) && $sidebarService::canSeeSalesManagement($currentUser)) {
                    $query = \App\Models\Department::with([
                        'category',
                        'pages' => fn($q) => $q->where('is_active', true)->orderBy('order')->orderBy('name'),
                    ]);

                    // For Super Admin, show all sales departments across all branches
                    if ($isSuperAdmin) {
                        $query->whereHas('category', fn($q) => $q->where('name', 'Sales'));
                    } else {
                        // For regular users, filter by branch
                        $query->where(fn($q) => $q->where('branch_id', $branchId)->orWhereNull('branch_id'))
                              ->whereHas('category', fn($q) => $q->where('name', 'Sales'));
                    }

                    $salesDepartments = $query->get();

                    // Only filter departments for non-Super Admin users
                    if (!$isSuperAdmin && !$sidebarService::isSalesAdminRole($currentUser)) {
                        if ($userDepartment && $currentUser->hasAnyRole($departmentRestrictedSalesRoles)) {
                            $salesDepartments = $salesDepartments->filter(fn($d) => $d->id === $userDepartment->id);
                        }
                    }

                    $salesDepartments = $salesDepartments->map(function ($dept) {
                        $dept->pages = $dept->pages->reject(
                            fn($p) => str_contains($p->route_name, 'edit') ||
                                str_contains($p->route_name, 'detail') ||
                                str_contains($p->route_name, 'shift-closing'),
                        );
                        return $dept;
                    });

                    $currentRoute = request()->route()?->getName();
                    $OPEN_SALES_DEPT = $salesDepartments->firstWhere(
                        fn($d) => $d->pages->pluck('route_name')->contains($currentRoute),
                    )?->id;
                    
                    // Exclude production roles from seeing sales departments
                    $productionRoles = [
                        'Head of Production',
                        'Chef',
                        'Head of Gelato',
                        'Confectionaries Manager',
                        'Kitchen Staff',
                        'Gelato Production Staff',
                        'Confectionaries Production Staff',
                    ];

                    $isProductionUser = $currentUser->hasAnyRole($productionRoles);
                    $OPEN_SALES = ($salesDepartments->isNotEmpty() || $OPEN_SALES_DEPT !== null) && !$isProductionUser;
                }
            @endphp

            @if ($OPEN_SALES)
            <flux:navlist.group :heading="__('Sales Departments')" icon="building-storefront">
                @forelse($salesDepartments as $dept)
                    <flux:navlist.group :heading="$dept->name" :badge="$dept->category?->name" expandable
                        :expanded="(request()->get('sales_dept_slug') == $dept->slug) ? true : false" class="grid">
                        @forelse($dept->pages as $page)
                            <flux:navlist.item icon="{{ $page->icon ?? 'o-shopping-bag' }}"
                                :href="branch_route($page->route_name, [
                                    'salesDeptSlug' => $dept->slug,
                                    'sales_dept_slug' => $dept->slug,
                                    'page' => $page->name . '_' . $dept->slug
                                ])"
                                :current="request()->get('page') === $page->name . '_' . $dept->slug" wire:navigate>
                                {{ $page->name }}
                            </flux:navlist.item>

                        @empty
                            <div class="pl-10 pr-4 py-1.5 text-xs text-gray-500 italic">
                                {{ __('No pages configured') }}
                            </div>
                        @endforelse
                    </flux:navlist.group>
                @empty
                    <div class="pl-10 pr-4 py-1.5 text-xs text-gray-500 italic">
                        {{ __('No sales departments') }}
                    </div>
                @endforelse
            </flux:navlist.group>
            @endif
            {{-- ====================RS 30793 SALES DEPARTMENTS MENU ==================== --}}

            {{-- ==================== REPORTING DASHBOARD ==================== --}}
            @if ($sidebarService::canSeeReporting($currentUser))
            <flux:navlist.group :heading="__('Reporting')" icon="document-text">
                <flux:navlist.item icon="chart-bar" :href="branch_route('branch-dashboard.reporting.dashboard')"
                    :current="request()->routeIs('branch-dashboard.reporting.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
                <flux:navlist.item icon="clipboard-document-check"
                    :href="branch_route('branch-dashboard.reporting.review')"
                    :current="request()->routeIs('branch-dashboard.reporting.review')" wire:navigate>
                    {{ __('Review Reports') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-duplicate"
                    :href="branch_route('branch-dashboard.reporting.compile')"
                    :current="request()->routeIs('branch-dashboard.reporting.compile')" wire:navigate>
                    {{ __('Compile Reports') }}
                </flux:navlist.item>
                <flux:navlist.item icon="paper-airplane" :href="branch_route('branch-dashboard.reporting.send-to-md')"
                    :current="request()->routeIs('branch-dashboard.reporting.send-to-md')" wire:navigate>
                    {{ __('Send to MD') }}
                </flux:navlist.item>
            </flux:navlist.group>
            @endif
            {{-- ==================== END REPORTING DASHBOARD ==================== --}}

           

        </flux:navlist>

        <flux:spacer />


        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="get_user_auth()?->name ?? 'User'" :initials="get_user_auth()?->initials() ?? 'U'"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ get_user_auth()?->initials() ?? 'U' }}
                                </span>
                            </span>
                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ get_user_auth()?->name ?? 'User' }}</span>
                                <span class="truncate text-xs">{{ get_user_auth()?->email ?? 'user@example.com' }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="branch_route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ branch_route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>
    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="get_user_auth()?->initials() ?? 'U'" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ get_user_auth()?->initials() ?? 'U' }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ get_user_auth()?->name ?? 'User' }}</span>
                                <span class="truncate text-xs">{{ get_user_auth()?->email ?? 'user@example.com' }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="branch_route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ branch_route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{-- navbar section --}}
    <flux:header sticky container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <!-- Sidebar Toggle -->
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <!-- Navbar Left -->
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')"
                :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <!-- Shift Status & Digital Clock -->
        <div class="flex items-center gap-6 text-sm text-zinc-700 dark:text-zinc-200 me-3">
            <!-- Shift Status Header Component -->
            @livewire('shift-status-header')

            <!-- Digital Clock -->
            <div x-data="{ currentTime: '' }" x-init="setInterval(() => {
                const now = new Date();
                currentTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }, 1000);"
                class="font-mono text-base md:text-lg tracking-widest" x-text="currentTime">
            </div>
        </div>

        <!-- Search Navbar -->
        <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
            <div x-data="{
                open: false,
                query: '',
                isMac: navigator.platform.toUpperCase().includes('MAC'),
                toggle() {
                    this.open = !this.open;
                    if (this.open) {
                        this.$nextTick(() => this.$refs.searchInput.focus());
                    }
                }
            }" x-init="window.addEventListener('keydown', e => {
                if ((isMac ? e.metaKey : e.ctrlKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    toggle();
                }
                if (e.key === 'Escape') open = false;
            });" class="relative">
                <button @click="toggle" type="button"
                    class="inline-flex items-center gap-3 px-3 py-1.5 rounded-full border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm hover:shadow-md transition-all duration-150">
                    <!-- Search icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-600 dark:text-zinc-300"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <span class="text-sm text-zinc-700 dark:text-zinc-200">Search</span>

                    <kbd
                        class="ml-1 inline-flex items-center gap-1 rounded border border-zinc-200 dark:border-zinc-700 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:text-zinc-300">
                        <span x-text="isMac ? '⌘' : 'Ctrl'"></span>
                        <span class="text-[10px] opacity-70">+</span>
                        <span>K</span>
                    </kbd>
                </button>

                <!-- Search Modal -->
                <div x-show="open" x-transition.opacity.duration.300ms @click.away="open = false"
                    class="fixed inset-0 bg-black/40 flex items-start justify-center pt-24 z-50" x-cloak>
                    <!-- Modal Box -->
                    <div x-show="open" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-4 scale-95" x-trap.noscroll.inert="open"
                        class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl w-full max-w-3xl p-6 mx-4">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100">Quick Search</h2>
                            <button @click="open = false"
                                class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Search Input -->
                        <form @submit.prevent="$dispatch('search', query); open = false">
                            <input x-ref="searchInput" type="text" x-model="query"
                                placeholder="Type to search..."
                                class="w-full border border-zinc-300 dark:border-zinc-700 rounded-lg p-3 text-base focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-zinc-800 dark:text-zinc-100" />
                        </form>

                        <!-- Footer -->
                        <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400 text-right">
                            Press <kbd class="px-1 py-0.5 border rounded">Esc</kbd> to close
                        </p>
                    </div>
                </div>
            </div>
        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="get_user_auth()?->initials() ?? 'U'" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ get_user_auth()?->initials() ?? 'U' }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ get_user_auth()?->name ?? 'User' }}</span>
                                <span class="truncate text-xs">{{ get_user_auth()?->email ?? 'user@example.com' }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>

    </flux:header>

    <flux:main>
        {{-- Branch Selector for Super Admins --}}
        @if (auth()->user() && auth()->user()->hasRole('Super Admin'))
            <livewire:components.branch-selector />
        @endif

        <x-toast />
        <x-dialog />
        {{ $slot }}
    </flux:main>

    @fluxScripts
    @livewireScripts
    @stack('scripts')
</body>

</html>
