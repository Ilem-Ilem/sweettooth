<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-900 p-4">
    <style>
        .dropdown-menu {
            transition: all 0.3s ease-in-out;
        }
        .dropdown-menu.hidden {
            opacity: 0;
            transform: translateY(-10px);
        }
        .dropdown-menu:not(.hidden) {
            opacity: 1;
            transform: translateY(0);
        }
        .shift-option:hover {
            background-color: #374151;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }
        .error-alert {
            animation: shake 0.3s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%, 75% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
        }
        .card {
            background: linear-gradient(145deg, #1f2937, #111827);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5), inset 0 1px 3px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.5); }
            50% { box-shadow: 0 0 30px rgba(34, 197, 94, 0.8); }
        }
        .active-shift-card {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>

    <!-- Active Shift View -->
    @if($hasActiveShift && $currentShift)
    <div class="w-full max-w-2xl p-8 card rounded-2xl bg-zinc-900 active-shift-card">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-green-400">Active Shift</h1>
            <p class="text-gray-400 mt-2">You are currently clocked in</p>
        </div>

        <!-- Shift Details Card -->
        <div class="bg-zinc-800 rounded-xl p-6 mb-6 border border-zinc-700">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-400 mb-1">Shift Number</p>
                    <p class="text-lg font-semibold text-white">{{ $currentShift->shift_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Shift Type</p>
                    <p class="text-lg font-semibold text-white capitalize">{{ str_replace('_', ' ', $currentShift->shift_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Clock In</p>
                    <p class="text-lg font-semibold text-green-400">{{ $currentShift->clock_in->format('h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Time Worked</p>
                    <p class="text-lg font-semibold text-blue-400">{{ $this->getTotalHoursWorked() }}</p>
                </div>
            </div>

            @if($currentShift->notes)
            <div class="mt-4 pt-4 border-t border-zinc-700">
                <p class="text-sm text-gray-400 mb-1">Notes</p>
                <p class="text-white">{{ $currentShift->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-4">
            <button wire:click="continueToWork"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl shadow-lg transition duration-200 font-semibold flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                Continue to Work
            </button>

            <button wire:click="clockOut"
                    wire:confirm="Are you sure you want to clock out?"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl shadow-lg transition duration-200 font-semibold flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span wire:loading.remove wire:target="clockOut">Clock Out</span>
                <span wire:loading wire:target="clockOut">Clocking out...</span>
            </button>
        </div>
    </div>

    @else
    <!-- Clock In View -->
    <div x-data="{
        selectedShift: '',
        isOpen: false,
        errorMessage: '',
        checkShiftTime(shift) {
            const now = new Date();
            const currentHour = now.getHours();
            const currentMinutes = now.getMinutes();
            const currentTime = currentHour + (currentMinutes / 60);
            const afternoonStart = 13; // 1:00 PM
            if (shift === 'Afternoon' && currentTime < afternoonStart) {
                this.errorMessage = 'Not yet time for Afternoon shift! It starts at 1:00 PM.';
                return false;
            }
            if (shift === 'Morning' && currentTime >= afternoonStart) {
                this.errorMessage = 'Morning shift is over! It ends at 1:00 PM.';
                return false;
            }
            this.errorMessage = '';
            return true;
        }
    }" class="w-full max-w-md p-8 card rounded-2xl bg-zinc-900">

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-100">Clock In</h1>
            <p class="text-gray-400 mt-2">Select your shift to start working</p>
        </div>

        <!-- Current Date and Time -->
        <div class="bg-zinc-800 rounded-xl p-4 mb-6 text-center border border-zinc-700">
            <p class="text-gray-400 text-sm">{{ now()->format('l, F j, Y') }}</p>
            <p class="text-2xl font-bold text-white mt-1" x-data x-text="new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })"></p>
        </div>

        <!-- Shift Selection -->
        <div class="relative bg-zinc-900 mb-4">
            <button
                @click="isOpen = !isOpen"
                class="w-full bg-zinc-800 text-left px-5 py-3 rounded-xl shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex justify-between items-center hover:bg-zinc-700 transition duration-200 border border-zinc-700"
            >
                <span x-text="selectedShift || 'Choose a shift'" class="text-gray-200"></span>
                <svg class="w-5 h-5 transform transition-transform duration-300" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div
                x-show="isOpen"
                @click.away="isOpen = false"
                class="dropdown-menu absolute w-full mt-2 bg-zinc-800 rounded-xl shadow-xl z-10 border border-zinc-700"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                x-cloak
            >
                <div
                    @click="if (checkShiftTime('Morning')) {
                        selectedShift = 'Morning Shift (6 AM - 1 PM)';
                        isOpen = false;
                        $wire.set('shift_type', 'morning');
                    }"
                    class="shift-option px-5 py-3 cursor-pointer text-gray-200 hover:text-blue-400 rounded-t-xl border-b border-zinc-700"
                >
                    <div class="font-semibold">Morning Shift</div>
                    <div class="text-sm text-gray-400">6:00 AM - 1:00 PM</div>
                </div>

                <div
                    @click="if (checkShiftTime('Afternoon')) {
                        selectedShift = 'Afternoon Shift (1 PM - 8 PM)';
                        isOpen = false;
                        $wire.set('shift_type', 'afternoon');
                    }"
                    class="shift-option px-5 py-3 cursor-pointer text-gray-200 hover:text-blue-400 border-b border-zinc-700"
                >
                    <div class="font-semibold">Afternoon Shift</div>
                    <div class="text-sm text-gray-400">1:00 PM - 8:00 PM</div>
                </div>

                <div
                    @click="selectedShift = 'Full Time (No Shift)';
                            $wire.set('shift_type', 'full_time');
                            isOpen = false;
                            errorMessage = ''"
                    class="shift-option px-5 py-3 cursor-pointer text-gray-200 hover:text-blue-400 rounded-b-xl"
                >
                    <div class="font-semibold">Full Time</div>
                    <div class="text-sm text-gray-400">No specific shift hours</div>
                </div>
            </div>
        </div>

        <!-- Notes (Optional) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-300 mb-2">Notes (Optional)</label>
            <textarea wire:model="notes" rows="3"
                      class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Add any notes about your shift..."></textarea>
        </div>

        <!-- Error Message -->
        <div x-show="errorMessage" class="mb-6 p-4 bg-red-600 text-white rounded-xl text-center font-bold error-alert shadow-md" x-cloak>
            <p x-text="errorMessage"></p>
        </div>

        <!-- Selected Shift Display and Actions -->
        <div x-show="selectedShift && !errorMessage" class="mb-6" x-cloak>
            <div class="bg-blue-900/30 border border-blue-700 rounded-xl p-4 mb-4">
                <p class="text-sm text-gray-400">Selected Shift</p>
                <p class="text-lg font-semibold text-blue-400" x-text="selectedShift"></p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button
                    @click="selectedShift = ''; isOpen = false; errorMessage = ''; $wire.set('shift_type', '')"
                    class="bg-zinc-700 hover:bg-zinc-600 text-white px-6 py-3 rounded-xl shadow-lg transition duration-200 font-semibold"
                >
                    Clear
                </button>

                <button
                    wire:click="clockIn"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl shadow-lg transition duration-200 font-semibold flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span wire:loading.remove wire:target="clockIn">Clock In</span>
                    <span wire:loading wire:target="clockIn">Clocking in...</span>
                </button>
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-gray-500 text-sm">Time-based shift validation enabled</p>
        </div>
    </div>
    @endif

</div>
