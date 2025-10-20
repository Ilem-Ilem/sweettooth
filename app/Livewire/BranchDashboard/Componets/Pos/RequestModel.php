<?php

namespace App\Livewire\BranchDashboard\Componets\Pos;

use App\Models\Product;
use App\Models\ProductStock;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class RequestModel extends Component
{
    public array $requestedItems = [];

    public function getProductsProperty(): Collection
    {
        $q = Product::query();

        // if (strlen($this->search)) {
        //     $q->where('name', 'like', '%' . $this->search . '%');
        // }
        return $q->limit(50)->get();
    }

    public function addToRequestedItems($id) {
        array_push($this->requestedItems, $id);
    }

    public function requestItems(){
        
    }

    protected function getTodayStockForProduct(string $productId, bool $forUpdate = false): ?ProductStock
    {
        $q = ProductStock::query()
            ->whereDate('stock_date', Carbon::today())
            ->where('product_id', $productId);
        if ($forUpdate) {
            $q->lockForUpdate();
        }
    

        return $q->first();
    }

    protected function availableQuantity(?ProductStock $stock): float
    {
        if (! $stock) {
            return 0.0;
        }
        // available is closing quantity; if not up to date, compute
        $stock->updateCalculatedFields();

        return max(0, (float) $stock->closing_quantity);
    }

    public function render()
    {
        return view('livewire.branch-dashboard.componets.pos.request-model');
    }
}
