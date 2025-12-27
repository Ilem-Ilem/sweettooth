### How to Add This to an Inventory System

To integrate these Manager.io-inspired features into an existing or new **Laravel-based inventory system**:

1.  **Run Migrations**: Add the above migrations and run `php artisan migrate`. Seed initial data (e.g., locations) if needed.

2.  **Models and Relationships**:
    -   Create Eloquent models (e.g., `InventoryItem`, `InventoryKit`) with relationships (hasMany, belongsToMany for pivots).
    -   Example for kits: In `InventoryKit` model, `public function components() { return $this->belongsToMany(InventoryItem::class, 'inventory_kit_components')->withPivot('quantity'); }`

3.  **Quantity and COGS Management**:
    -   Use database transactions and observers/events to update `quantity_on_hand` and `current_average_cost` automatically.
    -   On sales invoice line save: Reduce quantity (or components for kits), calculate COGS (quantity * average_cost), post to accounting (e.g., debit COGS expense, credit Inventory asset).
    -   Weighted average cost: On purchase, recalculate average = ((current_qty * current_avg) + (new_qty * new_price)) / (current_qty + new_qty).

4.  **Integration Points**:
    -   Link to sales/purchases: When creating invoice lines, select inventory items/kits → auto-deduct stock on "post" or "complete" status.
    -   Transfers/Write-offs: Forms to move stock or adjust.
    -   Reports: Use Laravel queries or packages like Laravel Charts for inventory reports (stock levels, valuations).
    -   Multi-location: Track per-location quantities in pivot table.

5.  **Additional Enhancements**:
    -   Use Laravel policies for permissions.
    -   Add soft deletes, audits (e.g., via Laravel Auditor package).
    -   For full accounting, add a simple `journal_entries` table linked to transactions.

This provides a solid foundation mimicking Manager.io's inventory capabilities, with automatic stock tracking tied to sales/purchases. Extend as needed for full accounting.
