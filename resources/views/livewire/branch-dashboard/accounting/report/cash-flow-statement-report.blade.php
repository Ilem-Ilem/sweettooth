<div>
    <x-slot:title>
        Cash Flow Statement
    </x-slot:title>





                    <div class="mb-6 flex flex-wrap items-end gap-4 space-x-4">
                        <div class="flex-grow">
                            <x-label for="startDate" value="Start Date" class="text-sm font-medium text-gray-700" />
                            <x-input id="startDate" type="date" wire:model.live="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                        </div>
                        <div class="flex-grow">
                            <x-label for="endDate" value="End Date" class="text-sm font-medium text-gray-700" />
                            <x-input id="endDate" type="date" wire:model.live="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                        </div>
                        <div class="flex-shrink-0">
                            <x-button wire:click="resetFilters" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring focus:ring-gray-300 focus:ring-opacity-50 transition ease-in-out duration-150">
                                Reset
                            </x-button>
                        </div>
                        <div class="ml-auto flex-shrink-0">
                            <x-button wire:click="exportToCsv" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-500 focus:ring-opacity-50 transition ease-in-out duration-150">
                                Export to CSV
                            </x-button>
                        </div>
                    </div>

                    <div class="space-y-8 mt-8">
                        {{-- Operating Activities --}}
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h4 class="text-2xl font-bold text-gray-800 mb-4">Operating Activities</h4>
                            <div class="space-y-2 text-lg">
                                @foreach($cfs['operating_activities'] ?? [] as $item => $amount)
                                    <div class="flex justify-between py-1 border-b border-gray-200">
                                        <span class="text-gray-700">{{ $item }}</span>
                                        <span class="font-medium text-gray-900">{{ number_format($amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between py-3 font-extrabold text-xl bg-blue-100 px-4 rounded-b-lg mt-4">
                                <span>Net Cash From Operating Activities</span>
                                <span>{{ number_format($cfs['operating']['total'], 2) }}</span>
                            </div>
                        </div>

                        {{-- Investing Activities --}}
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h4 class="text-2xl font-bold text-gray-800 mb-4">Investing Activities</h4>
                            <div class="space-y-2 text-lg">
                                @foreach($cfs['investing_activities'] ?? [] as $item => $amount)
                                    <div class="flex justify-between py-1 border-b border-gray-200">
                                        <span class="text-gray-700">{{ $item }}</span>
                                        <span class="font-medium text-gray-900">{{ number_format($amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between py-3 font-extrabold text-xl bg-blue-100 px-4 rounded-b-lg mt-4">
                                <span>Net Cash From Investing Activities</span>
                                <span>{{ number_format($cfs['investing']['total'], 2) }}</span>
                            </div>
                        </div>

                        {{-- Financing Activities --}}
                        <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                            <h4 class="text-2xl font-bold text-gray-800 mb-4">Financing Activities</h4>
                            <div class="space-y-2 text-lg">
                                @foreach($cfs['financing_activities'] ?? [] as $item => $amount)
                                    <div class="flex justify-between py-1 border-b border-gray-200">
                                        <span class="text-gray-700">{{ $item }}</span>
                                        <span class="font-medium text-gray-900">{{ number_format($amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between py-3 font-extrabold text-xl bg-blue-100 px-4 rounded-b-lg mt-4">
                                <span>Net Cash From Financing Activities</span>
                                <span>{{ number_format($cfs['financing']['total'], 2) }}</span>
                            </div>
                        </div>

                        {{-- Overall Cash Summary --}}
                        <div class="mt-8 pt-6 border-t-2 border-gray-300 space-y-3">
                            <div class="flex justify-between py-2 font-extrabold text-2xl bg-blue-500 text-white px-4 rounded-lg shadow-md">
                                <span>Net Increase (Decrease) in Cash</span>
                                <span>{{ number_format($cfs['net_change'], 2) }}</span>
                            </div>

                            <div class="flex justify-between py-2 font-extrabold text-xl bg-blue-200 px-4 rounded-lg shadow-sm">
                                <span>Cash at Beginning of Period</span>
                                <span>{{ number_format($cfs['opening_cash'], 2) }}</span>
                            </div>

                            <div class="flex justify-between py-3 font-extrabold text-2xl bg-blue-700 text-white px-4 rounded-lg shadow-lg">
                                <span>Cash at End of Period</span>
                                <span>{{ number_format($cfs['closing_cash'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bank Positions Summary --}}
                    <div class="mt-12 p-6 bg-white rounded-lg shadow-md border border-gray-200">
                        <h4 class="text-2xl font-bold text-gray-800 mb-4 flex justify-between items-center">
                            Bank Positions Summary
                            <x-button wire:click="toggleBankSummary" class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition ease-in-out duration-150">
                                {{ $showBankSummary ? 'Hide' : 'Show' }}
                            </x-button>
                        </h4>
                        @if ($showBankSummary)
                            <div class="overflow-x-auto mt-4">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Account</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Opening Balance</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deposits</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Withdrawals</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Closing Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($bankPositions ?? [] as $position)
                                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ is_array($position) && isset($position['bank_account']) ? $position['bank_account'] : $position }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($position['opening_balance'], 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ number_format($position['total_deposits'], 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($position['total_withdrawals'], 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($position['closing_balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        @if(empty($bankPositions))
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No bank positions available for the selected period.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Cash Positions Summary --}}
                    <div class="mt-12 p-6 bg-white rounded-lg shadow-md border border-gray-200">
                        <h4 class="text-2xl font-bold text-gray-800 mb-4 flex justify-between items-center">
                            Cash Positions Summary
                            <x-button wire:click="toggleCashSummary" class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition ease-in-out duration-150">
                                {{ $showCashSummary ? 'Hide' : 'Show' }}
                            </x-button>
                        </h4>
                        @if ($showCashSummary)
                            <div class="overflow-x-auto mt-4">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Opening Balance</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Inflows</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Outflows</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Closing Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($cashPositions ?? [] as $position)
                                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $position['location'] }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ number_format($position['opening_balance'], 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ number_format($position['total_inflows'], 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($position['total_outflows'], 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($position['closing_balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        @if(empty($cashPositions))
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No cash positions available for the selected period.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

</div>
