<div class="p-3 space-y-3">
    <x-breadcrumb title="Item Requests" :links="[['label' => 'Dashboard', 'url' => route('super-admin.dashboard')], ['label' => 'Inventory'], ['label' => 'Item Requests']]" />

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-700">Filters</h3>
            <button wire:click="resetFilters" class="text-xs text-blue-600 hover:text-blue-800">
                Reset Filters
            </button>
        </div>
        <div class="p-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" wire:model.live="search" placeholder="Request number or requester..."
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                    <select wire:model.live="filterBranch"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                    <select wire:model.live="filterDepartment"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="filterStatus"
                        class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="partially_dispatched">Partially Dispatched</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-3 py-2 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-700">Item Requests List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Request #</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requested By</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Request Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Required Date</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ $request->request_number }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $request->branch->name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $request->department->name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $request->requester->name }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $request->request_date->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $request->required_date->format('Y-m-d') }}</td>
                            <td class="px-3 py-2 text-xs text-gray-600">{{ $request->requestDetails->count() }}</td>
                            <td class="px-3 py-2 text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $request->status === 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $request->status === 'partially_dispatched' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $request->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <div class="flex space-x-2">
                                    @can('approve-item-requests')
                                        @if ($request->status === 'pending')
                                            <button wire:click="openApprovalModal({{ $request->id }})"
                                                class="text-green-600 hover:text-green-800">
                                                Approve
                                            </button>
                                            <button wire:click="rejectRequest({{ $request->id }})"
                                                onclick="return confirm('Are you sure you want to reject this request?')"
                                                class="text-red-600 hover:text-red-800">
                                                Reject
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-4 text-center text-sm text-gray-500">
                                No item requests found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Approval Modal -->
    @if ($showApprovalModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-4 border w-full max-w-md shadow-lg rounded-lg bg-white">
                <div class="px-3 py-2 border-b border-gray-200">
                    <h3 class="text-base font-medium text-gray-900">Approve Item Request</h3>
                </div>
                <div class="p-3">
                    <form wire:submit.prevent="approveRequest" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Approval Notes</label>
                            <textarea wire:model="approvalNotes" rows="3"
                                class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Optional notes..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" wire:click="closeModal"
                                class="px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-3 py-1.5 bg-green-600 text-white rounded-md text-xs font-medium hover:bg-green-700">
                                Approve Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
