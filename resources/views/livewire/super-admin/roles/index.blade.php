<div>
<x-table :$headers :$rows selectable wire:model="selectedIds"  striped paginate persist  :filter="['quantity' => 'quantity', 'search' => 'search']"
             :quantity="[2,5,10]" />
</div>
