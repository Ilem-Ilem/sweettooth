<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Models\ItemRequest;
use App\Models\ProductionRequest;
use App\Models\Recipe;
use App\Models\Shift;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Url};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class ProductionRequests extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $b_id;

    // Filters
    public $search = '';
    public $filterShift = '';
    public $filterRecipe = '';

    // Table settings
    public $table_quantity = 15;

    // Form fields
    public $productionRequestId;
    public $item_request_id = '';
    public $shift_id = '';
    public $recipe_id = '';
    public $planned_production_quantity = '';
    public $notes = '';

    // Modal states
    public $showModal = false;
    public $isEditing = false;
    public $showDetailModal = false;
    public $selectedProductionRequest = null;

    protected $updatesQueryString = ['search', 'filterShift', 'filterRecipe'];

    protected $rules = [
        'item_request_id' => 'required|exists:item_requests,id',
        'shift_id' => 'required|exists:shifts,id',
        'recipe_id' => 'nullable|exists:recipes,id',
        'planned_production_quantity' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'item_request_id.required' => 'Please select an item request.',
        'shift_id.required' => 'Please select a shift.',
        'planned_production_quantity.required' => 'Planned quantity is required.',
        'planned_production_quantity.min' => 'Planned quantity must be at least 0.01.',
    ];

    public function getBranchId()
    {
        return $this->b_id ? $this->b_id : request()->query('b_id');
    }

    public function render()
    {
        $branchId = $this->getBranchId();

        // Query production requests
        $query = ProductionRequest::with([
            'itemRequest.department',
            'itemRequest.requester',
            'shift.employee',
            'recipe'
        ])
            ->forBranch($branchId)
            ->when($this->search, function ($q) {
                $q->whereHas('itemRequest', function ($query) {
                    $query->where('request_number', 'like', '%' . $this->search . '%');
                })->orWhereHas('recipe', function ($query) {
                    $query->where('product_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterShift, fn($q) => $q->where('shift_id', $this->filterShift))
            ->when($this->filterRecipe, fn($q) => $q->where('recipe_id', $this->filterRecipe))
            ->orderBy('created_at', 'desc');

        $productionRequests = $query->paginate($this->table_quantity);

        // Get pending approved item requests for the branch
        $pendingItemRequests = ItemRequest::where('branch_id', $branchId)
            ->where('status', 'approved')
            ->whereDoesntHave('productionRequests')
            ->with(['department', 'requestDetails.item'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get active shifts for the branch
        $shifts = Shift::where('branch_id', $branchId)
            ->where('status', 'active')
            ->with(['employee', 'department'])
            ->orderBy('shift_date', 'desc')
            ->get();

        // Get active recipes for the branch
        $recipes = Recipe::where('branch_id', $branchId)
            ->where('status', 'active')
            ->orderBy('product_name')
            ->get();

        return view('livewire.branch-dashboard.production.production-requests', [
            'productionRequests' => $productionRequests,
            'pendingItemRequests' => $pendingItemRequests,
            'shifts' => $shifts,
            'recipes' => $recipes,
        ]);
    }

    public function openCreateModal()
    {
        // $this->authorize('create-production-requests'); // TODO: Enable permissions after testing
        $this->resetFields();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function save()
    {
        // $this->authorize('create-production-requests'); // TODO: Enable permissions after testing
        $this->validate();

        DB::beginTransaction();
        try {
            // Verify the item request is approved
            $itemRequest = ItemRequest::findOrFail($this->item_request_id);
            if ($itemRequest->status !== 'approved') {
                session()->flash('error', 'Only approved item requests can be converted to production requests.');
                return;
            }

            // Create production request
            ProductionRequest::create([
                'item_request_id' => $this->item_request_id,
                'shift_id' => $this->shift_id,
                'recipe_id' => $this->recipe_id ?: null,
                'planned_production_quantity' => $this->planned_production_quantity,
                'notes' => $this->notes,
            ]);

            DB::commit();
            session()->flash('success', 'Production request created successfully.');
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating production request: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // $this->authorize('edit-production-requests'); // TODO: Enable permissions after testing
        $productionRequest = ProductionRequest::findOrFail($id);

        $this->productionRequestId = $productionRequest->id;
        $this->item_request_id = $productionRequest->item_request_id;
        $this->shift_id = $productionRequest->shift_id;
        $this->recipe_id = $productionRequest->recipe_id;
        $this->planned_production_quantity = $productionRequest->planned_production_quantity;
        $this->notes = $productionRequest->notes;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function update()
    {
        // $this->authorize('edit-production-requests'); // TODO: Enable permissions after testing
        $this->validate();

        DB::beginTransaction();
        try {
            $productionRequest = ProductionRequest::findOrFail($this->productionRequestId);

            $productionRequest->update([
                'shift_id' => $this->shift_id,
                'recipe_id' => $this->recipe_id ?: null,
                'planned_production_quantity' => $this->planned_production_quantity,
                'notes' => $this->notes,
            ]);

            DB::commit();
            session()->flash('success', 'Production request updated successfully.');
            $this->closeModal();
            $this->resetFields();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating production request: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        // $this->authorize('delete-production-requests'); // TODO: Enable permissions after testing
        DB::beginTransaction();
        try {
            $productionRequest = ProductionRequest::findOrFail($id);
            $productionRequest->delete();

            DB::commit();
            session()->flash('success', 'Production request deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error deleting production request: ' . $e->getMessage());
        }
    }

    public function viewProductionRequest($id)
    {
        $this->selectedProductionRequest = ProductionRequest::with([
            'itemRequest.department',
            'itemRequest.requester',
            'itemRequest.branch',
            'itemRequest.requestDetails.item',
            'shift.employee',
            'shift.department',
            'recipe.ingredients.item'
        ])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
        $this->resetValidation();
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedProductionRequest = null;
    }

    public function resetFields()
    {
        $this->productionRequestId = null;
        $this->item_request_id = '';
        $this->shift_id = '';
        $this->recipe_id = '';
        $this->planned_production_quantity = '';
        $this->notes = '';
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filterShift = '';
        $this->filterRecipe = '';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterShift()
    {
        $this->resetPage();
    }

    public function updatedFilterRecipe()
    {
        $this->resetPage();
    }
}
