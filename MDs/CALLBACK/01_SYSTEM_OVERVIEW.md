# Callback System Overview

## Purpose
The Callback System manages the returns and issues reported between Production, Inventory, and Sales departments. It handles:
- **Sales → Production**: Product returns/issues from dispatches
- **Production → Inventory**: Defective raw materials or finished products
- **Inventory → Production**: Approval and stock updates

## System Architecture

```
PRODUCTION                    INVENTORY                     SALES
   ↓                             ↓                            ↓
DailyProduce          ProductionCallback            ProductDispatchCallback
   ↓                             ↓                            ↓
ProductDispatch           Stock Updates            ProductStock Updates
   ↓                                                          ↓
   └──────────────────────────────────────────────────────────┘
                    Unified Approval Workflow
```

## Three Main Callback Types

### 1. **ProductDispatchCallback** (Sales → Production)
**Flow**: Sales returns products back to Production
- **Initiation**: Sales team creates callback when products have issues
- **Approval**: Production approves the return
- **Completion**: Production receives and updates stock
- **Related Model**: `ProductDispatchCallback`
- **Table**: `product_dispatch_callbacks`

**Status Flow**:
```
pending → approved_by_production → received_by_production → completed
```

**Reasons**: expired, damaged, quality_issue, customer_return, over_received, wrong_item, other

---

### 2. **ProductionCallback** (Production → Inventory)
**Flow**: Production reports damaged raw materials or finished products
- **Initiation**: Production creates callback for damaged items
- **Two Types**:
  - `raw_material_from_stock`: Damaged raw materials
  - `finished_product_reject`: Damaged finished products
- **Approval**: Inventory approves
- **Completion**: Inventory marks complete after stock adjustments

**Status Flow**:
```
pending → approved_by_inventory → completed
        └─→ rejected (alternative path)
```

**Reasons**:
- Raw Material: damaged, expired, quality_issue, wrong_batch, contamination, other
- Finished Product: damaged, quality_issue, contamination, other

---

### 3. **ProductCallback** (Legacy - Sales Direct)
**Note**: This table appears to be legacy and is NOT actively used in current workflows.
- Defined in `create_product_callbacks_table` migration
- No corresponding Livewire components
- Should be reviewed for deprecation

---

## Key Relationships

### ProductDispatchCallback Relationships
```php
ProductDispatchCallback
├─ belongsTo(ProductDispatch)        // Original dispatch
├─ belongsTo(SalesShift)             // Sales shift initiating return
├─ belongsTo(Product)                // Product being returned
├─ belongsTo(Employee, 'recorded_by')    // Who reported it
├─ belongsTo(Employee, 'approved_by')    // Who approved (production)
└─ belongsTo(Employee, 'received_by')    // Who received (production)
```

### ProductionCallback Relationships
```php
ProductionCallback
├─ belongsTo(Shift)                  // Production shift
├─ belongsTo(Item)                   // Raw material (nullable)
├─ belongsTo(Product)                // Finished product (nullable)
├─ belongsTo(Employee, 'recorded_by')    // Who reported
├─ belongsTo(Employee, 'approved_by')    // Who approved (inventory)
└─ belongsTo(Recipe)                 // For finished products
```

---

## Data Casting & Storage

### ProductDispatchCallback
```php
protected $casts = [
    'quantity' => 'decimal:2',          // Decimal precision for accurate tracking
    'callback_time' => 'datetime',      // ISO format timestamp
    'approved_at' => 'datetime',
    'received_at' => 'datetime',
];
```

### ProductionCallback
```php
protected $casts = [
    'quantity' => 'decimal:2',
    'callback_time' => 'datetime',
    'approved_at' => 'datetime',
];
```

---

## Critical Features Implemented

✅ **Multi-Status Workflow**: All callbacks follow strict state machines
✅ **Employee Tracking**: Records who created, approved, and received
✅ **Timestamps**: All critical actions are timestamped
✅ **Stock Integration**: Callbacks automatically update stock levels
✅ **Rejection Support**: Production callbacks support rejection with notes
✅ **Notes & Reason Tracking**: Full audit trail via enum-based reasons
✅ **Scope Methods**: Efficient filtering by status and conditions

---

## Database Indexes

### ProductDispatchCallback Indexes
```sql
- (product_dispatch_id, status)
- (sales_shift_id, callback_time)
- (status, callback_time)
```

### ProductionCallback Indexes
```sql
- (shift_id, callback_time)
- (status, callback_time)
- (source_type, item_id)
```

These enable efficient querying for:
- Daily reports
- Status-based filtering
- Time-range queries
- Department-specific reports

---

## Connection to Production & Sales

**Production Linked Items**:
- `DailyProduce` - Tracks production quantities and callbacks
- `ProductDispatch` - Ships products to sales
- `Shift` - Production shift that created the product

**Sales Linked Items**:
- `ProductStock` - Sales inventory levels
- `SalesShift` - Sales shift that received products
- `ProductDispatch` - Original dispatch record

**Inventory Linked Items**:
- `Stock` - Raw material inventory
- `StockMovement` - Tracks all stock changes via callbacks
