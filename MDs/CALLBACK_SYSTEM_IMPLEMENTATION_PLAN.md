# Callback System Implementation Plan

## Overview
This document outlines the implementation of a comprehensive callback system for SweetTooth that enables two-way returns:
1. **Sales → Production**: Sales departments return bad/damaged items back to kitchen
2. **Production → Inventory**: Production departments return bad raw materials or reject finished products back to inventory

## Architecture Overview

### Current Flow (Forward)
```
Production → Product Dispatches → Sales Stock → Sales Transactions
```

### New Callback Flows (Reverse)
```
Sales → Product Dispatch Callbacks → Production
Production → Production Callbacks → Inventory
```

## Database Design

### 1. Sales-to-Production Callbacks (`product_dispatch_callbacks`)

**Purpose**: Track returns from sales back to production based on specific dispatches

| Field | Type | Description |
|-------|------|-------------|
| `id` | BIGINT PRIMARY KEY | Auto-increment ID |
| `product_dispatch_id` | BIGINT FK | Links to original product_dispatch |
| `sales_shift_id` | BIGINT FK | Sales shift initiating callback |
| `product_id` | UUID FK | Product being returned |
| `recorded_by` | UUID FK | Sales employee recording |
| `quantity` | DECIMAL(12,2) | Quantity being returned |
| `uom` | VARCHAR(50) | Unit of measure |
| `reason` | ENUM | expired, damaged, quality_issue, customer_return, over_received, wrong_item, other |
| `status` | ENUM | pending, approved_by_production, received_by_production, completed |
| `approved_by` | UUID FK | Production employee approving |
| `approved_at` | TIMESTAMP | Approval timestamp |
| `received_by` | UUID FK | Production employee receiving |
| `received_at` | TIMESTAMP | Receipt timestamp |
| `notes` | TEXT | Additional notes |
| `callback_time` | TIMESTAMP | When callback was recorded |
| `created_at/updated_at` | TIMESTAMPS | Standard timestamps |

**Indexes**:
- `(product_dispatch_id, status)`
- `(sales_shift_id, callback_time)`
- `(status, callback_time)`

### 2. Production-to-Inventory Callbacks (`production_callbacks`)

**Purpose**: Track returns from production back to inventory (raw materials or finished products)

| Field | Type | Description |
|-------|------|-------------|
| `id` | BIGINT PRIMARY KEY | Auto-increment ID |
| `shift_id` | BIGINT FK | Production shift initiating callback |
| `source_type` | ENUM | raw_material_from_stock, finished_product_reject |
| `item_id` | BIGINT FK | Raw material item (if applicable) |
| `product_id` | UUID FK | Finished product (if applicable) |
| `recorded_by` | UUID FK | Production employee recording |
| `quantity` | DECIMAL(12,2) | Quantity being returned |
| `uom` | VARCHAR(50) | Unit of measure |
| `reason` | ENUM | damaged, expired, quality_issue, wrong_batch, contamination, other |
| `status` | ENUM | pending, approved_by_inventory, completed, rejected |
| `approved_by` | UUID FK | Inventory employee approving |
| `approved_at` | TIMESTAMP | Approval timestamp |
| `notes` | TEXT | Additional notes |
| `callback_time` | TIMESTAMP | When callback was recorded |
| `created_at/updated_at` | TIMESTAMPS | Standard timestamps |

**Indexes**:
- `(shift_id, callback_time)`
- `(status, callback_time)`
- `(source_type, item_id)`

## Workflow States & Transitions

### Sales → Production Callback Flow

```
1. PENDING
   ↓ (Production Manager Approves)
2. APPROVED_BY_PRODUCTION
   ↓ (Production Staff Receives Items)
3. RECEIVED_BY_PRODUCTION
   ↓ (System Processes)
4. COMPLETED
```

### Production → Inventory Callback Flow

```
1. PENDING
   ↓ (Inventory Manager Approves)
2. APPROVED_BY_INVENTORY
   ↓ (System Processes Stock Adjustments)
3. COMPLETED
```

## Component Structure

### Sales Dashboard Components
- `Livewire/BranchDashboard/SalesDashboard/Callbacks/Index` (extend existing)
- `Livewire/BranchDashboard/SalesDashboard/Callbacks/CreateDispatchCallback`
- `Livewire/BranchDashboard/SalesDashboard/Callbacks/ManageDispatchCallbacks`

### Production Dashboard Components
- `Livewire/BranchDashboard/ProductionDashboard/Callbacks/Index`
- `Livewire/BranchDashboard/ProductionDashboard/Callbacks/CreateInventoryCallback`
- `Livewire/BranchDashboard/ProductionDashboard/Callbacks/ManageInventoryCallbacks`

### Inventory Dashboard Components
- `Livewire/BranchDashboard/InventoryDashboard/Callbacks/Index`
- `Livewire/BranchDashboard/InventoryDashboard/Callbacks/ApproveProductionCallbacks`

## Business Logic & Rules

### Quantity Validations
- **Sales Callbacks**: `callback_quantity ≤ product_dispatch.received_quantity - previous_callbacks`
- **Production Callbacks**: `callback_quantity ≤ available_stock` (for raw materials)

### Stock Impact Calculations

#### Sales → Production Callback Effects:
- **Sales Side**: `ProductStock.callback_quantity += callback_quantity`
- **Sales Side**: `ProductStock.total_available -= callback_quantity`
- **Production Side**: `DailyProduce.callback_quantity += callback_quantity`
- **Production Side**: `DailyProduce.closing_quantity += callback_quantity`

#### Production → Inventory Callback Effects:
- **Raw Material Return**: `stocks.quantity_available -= callback_quantity`, `stocks.quantity_damaged += callback_quantity`
- **Finished Product Reject**: `DailyProduce.callback_quantity += callback_quantity`

### Approval Requirements
- **Sales → Production**: Requires production department approval
- **Production → Inventory**: Requires inventory department approval
- **Manager Override**: Department managers can approve their own department's callbacks

## Implementation Phases

### Phase 1: Database Foundation
1. Create `product_dispatch_callbacks` migration
2. Create `production_callbacks` migration
3. Create corresponding Eloquent models
4. Run migrations and verify relationships

### Phase 2: Sales → Production Callbacks
1. Extend existing `Callbacks/Index` component
2. Create `CreateDispatchCallback` component
3. Add callback creation form to sales dashboard
4. Implement approval workflow for production

### Phase 3: Production → Inventory Callbacks
1. Create production callback components
2. Add callback creation form to production dashboard
3. Implement approval workflow for inventory
4. Connect to stock adjustment logic

### Phase 4: Integration & Testing
1. Connect all stock calculations
2. Implement notification system
3. Add callback history views
4. Test end-to-end workflows

## Security & Access Control

### Department-Based Permissions
- **Sales Employees**: Can only create sales-to-production callbacks
- **Production Employees**: Can only create production-to-inventory callbacks
- **Inventory Employees**: Can only approve production callbacks
- **Managers**: Can approve callbacks for their department

### Audit Trail Requirements
- All callback creations logged with employee ID and timestamp
- All status changes tracked
- Stock adjustments linked to callback records

## Integration Points

### Existing Systems
- **ProductStock Model**: Extend `calculateTotalAvailable()` to include dispatch callbacks
- **DailyProduce Model**: Update closing calculations to account for callbacks
- **Stock Model**: Add callback-based adjustments
- **Notification System**: Alert relevant departments of pending approvals

### Reporting Integration
- Add callback metrics to sales reports
- Include callback trends in production efficiency reports
- Track callback reasons for quality improvement

## Testing Scenarios

### Sales → Production
1. Create product dispatch from production to sales
2. Sales employee records callback for damaged items
3. Verify callback appears in production approval queue
4. Production approves and receives callback
5. Verify stock adjustments on both sides

### Production → Inventory
1. Production employee records bad raw material callback
2. Verify callback appears in inventory approval queue
3. Inventory approves callback
4. Verify stock damaged quantity increases
5. Verify available quantity
     decreases

## Performance Considerations

### Database Optimization
- Add proper indexes on frequently queried fields
- Consider partitioning callback tables by date if volume is high
- Use database transactions for stock adjustments

### UI Optimization
- Lazy load callback lists with pagination
- Cache department and employee data
- Implement real-time notifications for pending approvals

## Future Enhancements

### Advanced Features
- **Bulk Callbacks**: Allow multiple items in single callback
- **Callback Templates**: Pre-defined reasons and quantities
- **Automated Approvals**: Rules-based auto-approval for certain conditions
- **Callback Analytics**: Detailed reporting on callback patterns
- **Integration with Suppliers**: Callbacks that trigger supplier notifications

### Mobile Support
- Mobile-optimized callback forms
- Barcode scanning for callback items
- Push notifications for approvals

---

## Implementation Checklist

### Database Layer ✅
- [ ] Create `product_dispatch_callbacks` migration
- [ ] Create `production_callbacks` migration
- [ ] Create Eloquent models with relationships
- [ ] Add model scopes and helper methods

### Sales Components ⏳
- [ ] Extend `Callbacks/Index` for dispatch callbacks
- [ ] Create `CreateDispatchCallback` component
- [ ] Create callback form view
- [ ] Add approval workflow

### Production Components ⏳
- [ ] Create `Callbacks/Index` for production
- [ ] Create `CreateInventoryCallback` component
- [ ] Create callback form view
- [ ] Add approval workflow

### Integration ⏳
- [ ] Update ProductStock calculations
- [ ] Update DailyProduce calculations
- [ ] Update Stock model for damaged tracking
- [ ] Implement notification system

### Testing ✅
- [ ] Unit tests for models and calculations
- [ ] Integration tests for workflows
- [ ] End-to-end testing with sample data
- [ ] Performance testing with large datasets

---

**Document Version**: 1.0
**Last Updated**: 2025-11-02
**Next Review**: Implementation completion
