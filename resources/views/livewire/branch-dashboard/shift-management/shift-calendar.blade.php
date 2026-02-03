<div class="p-3 space-y-3">
    <x-breadcrumb
        title="Shift Calendar"
        :items="[
            ['label' => 'Dashboard', 'url' => route('branch-dashboard.index')],
            ['label' => 'Shift Management', 'url' => route('branch-dashboard.shift-management.index', ['b_id' => $b_id])],
            ['label' => 'Calendar']
        ]"
        :compact="false"
        :with-icons="true"
    />

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Shift Calendar</h1>
            <p class="text-zinc-600 dark:text-zinc-400">View shift assignments in calendar format</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Branch: <strong class="text-zinc-900 dark:text-white">{{ $branch->name ?? 'All' }}</strong></p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-3">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Filter by Department</label>
                <select wire:model.live="selectedDepartment" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Filter by Employee</label>
                <select wire:model.live="selectedEmployee" class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">&nbsp;</label>
                <div class="flex space-x-2">
                    <button wire:click="previousMonth" class="px-3 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg transition duration-200 font-medium text-sm">Previous</button>
                    <button wire:click="goToToday" class="px-3 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg transition duration-200 font-medium text-sm">Today</button>
                    <button wire:click="nextMonth" class="px-3 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-800 dark:text-zinc-200 rounded-lg transition duration-200 font-medium text-sm">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                {{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }}
            </h2>
        </div>

        <div class="grid grid-cols-7 gap-1">
            <!-- Day headers -->
            @foreach($getWeekDays() as $day)
                <div class="text-center py-2 font-semibold text-zinc-700 dark:text-zinc-300 text-sm">
                    {{ $day }}
                </div>
            @endforeach

            <!-- Empty cells for days before the first day of the month -->
            @for($i = 0; $i < $getStartDayOfMonth(); $i++)
                <div class="min-h-24 p-2 border border-transparent"></div>
            @endfor

            <!-- Days of the month -->
            @for($day = 1; $day <= $getDaysInMonth(); $day++)
                @php
                    $date = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                    $carbonDate = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
                    $isToday = $carbonDate->isSameDay(now());
                    $shifts = $getShiftsForDate($date);

                    $classes = 'min-h-24 p-2 border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800';
                    if ($carbonDate->isCurrentMonth()) {
                        $classes .= ' bg-zinc-50 dark:bg-zinc-750';
                    }
                    if ($isToday) {
                        $classes .= ' bg-blue-100 dark:bg-blue-800';
                    }
                @endphp

                <div class="{{ $classes }}">
                    <div class="text-right text-zinc-900 dark:text-white font-medium">{{ $day }}</div>

                    @if($shifts->count() > 0)
                        @foreach($shifts as $shift)
                            @php
                                $shiftClass = '';
                                switch($shift->shift_type) {
                                    case 'morning':
                                        $shiftClass = 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100';
                                        break;
                                    case 'afternoon':
                                        $shiftClass = 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100';
                                        break;
                                    case 'night':
                                        $shiftClass = 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100';
                                        break;
                                    default:
                                        $shiftClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100';
                                }
                            @endphp
                            <span class="block text-xs px-2 py-1 rounded mt-1 truncate w-full text-center {{ $shiftClass }}">
                                {{ $shift->employee->name }}
                            </span>
                        @endforeach
                    @else
                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">No shifts</div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-4">
        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-3">Shift Type Legend</h3>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center">
                <span class="block text-xs px-2 py-1 rounded mt-1 truncate w-full text-center bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 mr-2">Morning</span>
                <span class="text-sm text-zinc-700 dark:text-zinc-300">Morning Shift</span>
            </div>
            <div class="flex items-center">
                <span class="block text-xs px-2 py-1 rounded mt-1 truncate w-full text-center bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 mr-2">Afternoon</span>
                <span class="text-sm text-zinc-700 dark:text-zinc-300">Afternoon Shift</span>
            </div>
            <div class="flex items-center">
                <span class="block text-xs px-2 py-1 rounded mt-1 truncate w-full text-center bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100 mr-2">Night</span>
                <span class="text-sm text-zinc-700 dark:text-zinc-300">Night Shift</span>
            </div>
            <div class="flex items-center">
                <span class="block text-xs px-2 py-1 rounded mt-1 truncate w-full text-center bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 mr-2">Custom</span>
                <span class="text-sm text-zinc-700 dark:text-zinc-300">Custom Shift</span>
            </div>
        </div>
    </div>
</div>