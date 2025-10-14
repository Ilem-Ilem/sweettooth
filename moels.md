```php
<?php

namespace App\Models;

// ==================== INVENTORY MODELS ====================

class Item extends Model
{
    protected $fillable = [
        'branch_id', 'name', 'sku', 'category', 'uom', 'description',
        'reorder_level', 'max_stock_level', 'status'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function requestDetails()
    {
        return $this->hasMany(ItemRequestDetail::class);
    }
}

class Purchase extends Model
{
    protected $fillable = [
        'branch_id', 'recorded_by', 'purchase_number', 'purchase_date',
        'supplier_name', 'supplier_contact', 'total_fob_fc', 'total_fob_ngn',
        'other_costs', 'landing_cost', 'total_cost', 'currency',
        'exchange_rate', 'payment_status', 'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'item_id', 'quantity', 'uom', 'fob_fc', 'fob_ngn',
        'other_costs', 'landing_cost', 'total_cost', 'cost_per_unit'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

class Stock extends Model
{
    protected $fillable = [
        'branch_id', 'item_id', 'quantity_available', 'quantity_reserved',
        'quantity_damaged', 'average_cost', 'last_stock_take_date',
        'health_status', 'expiry_date'
    ];

    protected $casts = [
        'last_stock_take_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function healthChecks()
    {
        return $this->hasMany(HealthCheck::class);
    }
}

class StockMovement extends Model
{
    protected $fillable = [
        'stock_id', 'type', 'quantity', 'quantity_before', 'quantity_after',
        'reference_type', 'reference_id', 'moved_by', 'notes', 'movement_date'
    ];

    protected $casts = [
        'movement_date' => 'datetime',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function movedBy()
    {
        return $this->belongsTo(Employee::class, 'moved_by');
    }
}

class ItemRequest extends Model
{
    protected $fillable = [
        'branch_id', 'department_id', 'requested_by', 'request_number',
        'request_date', 'shift', 'status', 'approved_by', 'approved_at', 'notes'
    ];

    protected $casts = [
        'request_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(ItemRequestDetail::class, 'request_id');
    }

    public function dispatches()
    {
        return $this->hasMany(ItemDispatch::class, 'request_id');
    }
}

class ItemRequestDetail extends Model
{
    protected $fillable = [
        'request_id', 'item_id', 'quantity_requested', 'quantity_approved',
        'quantity_dispatched', 'uom', 'notes'
    ];

    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'request_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

class ItemDispatch extends Model
{
    protected $fillable = [
        'request_id', 'item_id', 'dispatched_by', 'received_by',
        'quantity', 'uom', 'dispatch_time', 'received_time', 'shift', 'notes'
    ];

    protected $casts = [
        'dispatch_time' => 'datetime',
        'received_time' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'request_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function dispatchedBy()
    {
        return $this->belongsTo(Employee::class, 'dispatched_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }
}

class StockTake extends Model
{
    protected $fillable = [
        'branch_id', 'stock_take_number', 'stock_take_date', 'type',
        'conducted_by', 'status', 'verified_by', 'verified_at', 'notes'
    ];

    protected $casts = [
        'stock_take_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function conductedBy()
    {
        return $this->belongsTo(Employee::class, 'conducted_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Employee::class, 'verified_by');
    }

    public function details()
    {
        return $this->hasMany(StockTakeDetail::class);
    }
}

class StockTakeDetail extends Model
{
    protected $fillable = [
        'stock_take_id', 'item_id', 'system_quantity', 'physical_quantity',
        'variance', 'variance_type', 'notes'
    ];

    public function stockTake()
    {
        return $this->belongsTo(StockTake::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

class HealthCheck extends Model
{
    protected $fillable = [
        'stock_id', 'checked_by', 'check_date', 'condition',
        'quantity_affected', 'observations', 'action_taken'
    ];

    protected $casts = [
        'check_date' => 'date',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function checkedBy()
    {
        return $this->belongsTo(Employee::class, 'checked_by');
    }
}

// ==================== PRODUCTION MODELS ====================

class Recipe extends Model
{
    protected $fillable = [
        'branch_id', 'department_id', 'product_name', 'sku', 'category_id',
        'product_type', 'cost_per_unit', 'uom', 'yield_quantity',
        'preparation_time', 'instructions', 'status', 'created_by'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function dailyProduces()
    {
        return $this->hasMany(DailyProduce::class);
    }

    public function productionRecords()
    {
        return $this->hasMany(ProductionRecord::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Calculate total cost based on ingredients
    public function calculateCost()
    {
        return $this->ingredients->sum(function ($ingredient) {
            $item = $ingredient->item;
            $stock = $item->stocks()->where('branch_id', $this->branch_id)->first();
            return ($stock ? $stock->average_cost : 0) * $ingredient->quantity;
        });
    }
}

class RecipeIngredient extends Model
{
    protected $fillable = [
        'recipe_id', 'item_id', 'quantity', 'uom', 'sort_order', 'notes'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

class Shift extends Model
{
    protected $fillable = [
        'branch_id', 'department_id', 'employee_id', 'shift_number',
        'shift_date', 'shift_type', 'clock_in', 'clock_out', 'status', 'notes'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function dailyProduces()
    {
        return $this->hasMany(DailyProduce::class);
    }

    public function productionRequests()
    {
        return $this->hasMany(ProductionRequest::class);
    }

    public function callBacks()
    {
        return $this->hasMany(CallBack::class);
    }

    public function rawMaterialUtilizations()
    {
        return $this->hasMany(RawMaterialUtilization::class);
    }
}

class DailyProduce extends Model
{
    protected $fillable = [
        'shift_id', 'recipe_id', 'produce_date', 'shift_type',
        'opening_quantity', 'requested_quantity', 'produced_quantity',
        'sent_out_quantity', 'order_quantity', 'callback_quantity',
        'closing_quantity', 'expected_closing', 'variance', 'notes'
    ];

    protected $casts = [
        'produce_date' => 'date',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function productionRecords()
    {
        return $this->hasMany(ProductionRecord::class);
    }

    // Auto-calculate expected closing
    public function calculateExpectedClosing()
    {
        return $this->opening_quantity 
            + $this->produced_quantity 
            - $this->sent_out_quantity 
            - $this->callback_quantity;
    }

    // Calculate variance
    public function calculateVariance()
    {
        return $this->closing_quantity - $this->calculateExpectedClosing();
    }
}

class ProductionRecord extends Model
{
    protected $fillable = [
        'daily_produce_id', 'recipe_id', 'produced_by', 'quantity_produced',
        'quantity_approved', 'quantity_rejected', 'production_time',
        'quality_status', 'rejection_reason', 'notes'
    ];

    protected $casts = [
        'production_time' => 'datetime',
    ];

    public function dailyProduce()
    {
        return $this->belongsTo(DailyProduce::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function producedBy()
    {
        return $this->belongsTo(Employee::class, 'produced_by');
    }
}

class ProductionRequest extends Model
{
    protected $fillable = [
        'shift_id', 'item_request_id', 'recipe_id',
        'planned_production_quantity', 'notes'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}

class CallBack extends Model
{
    protected $fillable = [
        'shift_id', 'callback_type', 'reference_id', 'quantity', 'uom',
        'reason', 'description', 'reported_by', 'callback_time', 'action_taken'
    ];

    protected $casts = [
        'callback_time' => 'datetime',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(Employee::class, 'reported_by');
    }
}

class RawMaterialUtilization extends Model
{
    protected $fillable = [
        'shift_id', 'recipe_id', 'item_id', 'quantity_required',
        'quantity_used', 'units_produced', 'variance', 'variance_type',
        'cost_impact', 'notes'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

// ==================== SALES MODELS ====================

class ProductCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'type'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'category_id');
    }
}

class Product extends Model
{
    protected $fillable = [
        'branch_id', 'department_id', 'category_id', 'recipe_id',
        'name', 'sku', 'description', 'unit_price', 'source',
        'available_for_glovo', 'available_for_transfer', 'status', 'image'
    ];

    protected $casts = [
        'available_for_glovo' => 'boolean',
        'available_for_transfer' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function kitchenOrders()
    {
        return $this->hasMany(KitchenOrder::class);
    }
}

class Table extends Model
{
    protected $fillable = [
        'branch_id', 'table_number', 'capacity', 'status', 'location'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function tableOrders()
    {
        return $this->hasMany(TableOrder::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}

class SalesShift extends Model
{
    protected $fillable = [
        'branch_id', 'department_id', 'employee_id', 'shift_number',
        'shift_date', 'shift_type', 'clock_in', 'clock_out',
        'opening_cash', 'closing_cash', 'expected_cash', 'cash_variance',
        'status', 'verified_by', 'notes'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Employee::class, 'verified_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function tableOrders()
    {
        return $this->hasMany(TableOrder::class);
    }

    public function kitchenOrders()
    {
        return $this->hasMany(KitchenOrder::class);
    }
}

class Sale extends Model
{
    protected $fillable = [
        'sales_shift_id', 'branch_id', 'department_id', 'sold_by',
        'sale_number', 'table_id', 'table_number', 'sale_time',
        'subtotal', 'tax', 'discount', 'total', 'status',
        'order_type', 'notes'
    ];

    protected $casts = [
        'sale_time' => 'datetime',
    ];

    public function salesShift()
    {
        return $this->belongsTo(SalesShift::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function soldBy()
    {
        return $this->belongsTo(Employee::class, 'sold_by');
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'unit_price',
        'subtotal', 'discount', 'total', 'notes'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

class Payment extends Model
{
    protected $fillable = [
        'sale_id', 'payment_method', 'amount', 'reference_number',
        'payment_time', 'status', 'notes'
    ];

    protected $casts = [
        'payment_time' => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}

class TableOrder extends Model
{
    protected $fillable = [
        'table_id', 'sales_shift_id', 'served_by', 'order_number',
        'order_time', 'guest_count', 'total_amount', 'order_status',
        'payment_status', 'is_cleared', 'cleared_at', 'notes'
    ];

    protected $casts = [
        'order_time' => 'datetime',
        'cleared_at' => 'datetime',
        'is_cleared' => 'boolean',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function salesShift()
    {
        return $this->belongsTo(SalesShift::class);
    }

    public function servedBy()
    {
        return $this->belongsTo(Employee::class, 'served_by');
    }

    public function items()
    {
        return $this->hasMany(TableOrderItem::class);
    }
}

class TableOrderItem extends Model
{
    protected $fillable = [
        'table_order_id', 'product_id', 'quantity', 'unit_price',
        'total', 'status', 'special_instructions'
    ];

    public function tableOrder()
    {
        return $this->belongsTo(TableOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

class ProductStock extends Model
{
    protected $fillable = [
        'sales_shift_id', 'product_id', 'stock_date', 'shift_type',
        'opening_quantity', 'addition_quantity', 'callback_quantity',
        'redress_quantity', 'total_available', 'transfer_quantity',
        'glovo_quantity', 'quantity_sold', 'closing_quantity', 'amount', 'notes'
    ];

    protected $casts = [
        'stock_date' => 'date',
    ];

    public function salesShift()
    {
        return $this->belongsTo(SalesShift::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Auto-calculate total available
    public function calculateTotalAvailable()
    {
        return $this->opening_quantity 
            + $this->addition_quantity 
            - $this->callback_quantity 
            + $this->redress_quantity;
    }

    // Auto-calculate closing
    public function calculateClosing()
    {
        return $this->calculateTotalAvailable() 
            - $this->transfer_quantity 
            - $this->glovo_quantity 
            - $this->quantity_sold;
    }
}

class Transfer extends Model
{
    protected $fillable = [
        'branch_id', 'from_department_id', 'to_department_id', 'product_id',
        'transfer_number', 'transferred_by', 'received_by', 'quantity',
        'transfer_time', 'received_time', 'status', 'rejection_reason', 'notes'
    ];

    protected $casts = [
        'transfer_time' => 'datetime',
        'received_time' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transferredBy()
    {
        return $this->belongsTo(Employee::class, 'transferred_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }
}

class KitchenOrder extends Model
{
    protected $fillable = [
        'sales_shift_id', 'production_department_id', 'product_id',
        'order_number', 'ordered_by', 'quantity_ordered', 'quantity_received',
        'order_time', 'expected_ready_time', 'actual_ready_time',
        'received_time', 'status', 'priority', 'notes'
    ];

    protected $casts = [
        'order_time' => 'datetime',
        'expected_ready_time' => 'datetime',
        'actual_ready_time' => 'datetime',
        'received_time' => 'datetime',
    ];

    public function salesShift()
    {
        return $this->belongsTo(SalesShift::class);
    }

    public function productionDepartment()
    {
        return $this->belongsTo(Department::class, 'production_department_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderedBy()
    {
        return $this->belongsTo(Employee::class, 'ordered_by');
    }
}
