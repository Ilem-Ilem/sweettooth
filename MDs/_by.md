# Migrations with "_by" Columns

This document lists all migration files containing columns with "_by" pattern (e.g., requested_by, moved_by, etc.)

## Summary
Total files with "_by" columns: 26

---

## Detailed List

### 1. 2025_11_09_103341_create_employee_leave_allocations_table.php
- **Line 21**: `$table->uuid('allocated_by')->nullable(); // Who allocated this`
- **Line 27**: `$table->foreign('allocated_by')->references('id')->on('employees')->onDelete('set null');`

### 2. 2025_10_23_120000_create_product_dispatches_table.php
- **Line 28**: `$table->uuid('dispatched_by');`
- **Line 29**: `$table->foreign('dispatched_by')->references('id')->on('employees')->onDelete('cascade');`
- **Line 38**: `$table->uuid('received_by')->nullable()->comment('Sales employee who received');`
- **Line 39**: `$table->foreign('received_by')->references('id')->on('employees')->onDelete('set null');`

### 3. 2025_11_14_000001_create_department_reports_table.php
- **Line 18**: `$table->foreignUuid('generated_by')->nullable()->constrained('employees')->nullOnDelete();`
- **Line 32**: `$table->foreignUuid('reviewed_by')->nullable()->constrained('employees')->nullOnDelete();`

### 4. 2025_10_09_012627_create_health_checks_table.php
- **Line 18**: `$table->uuid('checked_by_id');`
- **Line 19**: `$table->string('checked_by_type');`
- **Line 20**: `// $table->foreign('checked_by')->references('id')->on('employees')->onDelete('restrict');`

### 5. 2025_10_09_012613_create_purchases_table.php
- **Line 18**: `$table->uuid('recorded_by_id');`
- **Line 19**: `$table->string('recorded_by_type');`
- **Line 20**: `// $table->morphs('recorded_by');`
- **Line 21**: `// $table->foreign('recorded_by')->references('id')->on('employees')->onDelete('restrict');`

### 6. 2025_10_09_012617_create_stock_movements_table.php
- **Line 24**: `// $table->uuid('moved_by')->nullable();`
- **Line 25**: `// $table->foreign('moved_by')->references('id')->on('employees')->onDelete('restrict');`
- **Line 26**: `// $table->morphs('moved_by');`
- **Line 28**: `$table->string('moved_by_type')->nullable();`
- **Line 29**: `$table->uuid('moved_by_id')->nullable();`
- **Line 32**: `$table->index(['moved_by_type', 'moved_by_id']);`

### 7. 2025_10_09_012619_create_item_requests_table.php
- **Line 30**: `$table->uuidMorphs('requested_by');           // created_by_id + created_by_type`
- **Line 33**: `$table->uuidMorphs('approved_by');   // approved_by_id + approved_by_type`
- **Line 34**: `$table->uuidMorphs('cancelled_by');`
- **Line 35**: `$table->uuidMorphs('dispatched_by');`

### 8. 2025_11_09_053507_create_employee_stepouts_table.php
- **Line 29**: `$table->uuid('approved_by')->nullable();`
- **Line 31**: `$table->uuid('rejected_by')->nullable();`
- **Line 39**: `$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');`
- **Line 40**: `$table->foreign('rejected_by')->references('id')->on('employees')->onDelete('set null');`

### 9. 2025_11_09_053151_create_probation_reviews_table.php
- **Line 33**: `$table->uuid('acknowledged_by')->nullable();`
- **Line 39**: `$table->foreign('acknowledged_by')->references('id')->on('employees')->onDelete('set null');`

### 10. 2025_11_14_000002_create_compiled_reports_table.php
- **Line 17**: `$table->foreignUuid('compiled_by')->nullable()->constrained('employees')->nullOnDelete();`
- **Line 30**: `$table->enum('status', ['draft', 'pending_approval', 'approved', 'sent_to_md', 'reviewed_by_md'])->default('draft');`
- **Line 31**: `$table->foreignUuid('approved_by')->nullable()->constrained('employees')->nullOnDelete();`

### 11. 2025_11_02_104432_create_production_callbacks_table.php
- **Line 36**: `$table->uuid('recorded_by');`
- **Line 37**: `$table->foreign('recorded_by')->references('id')->on('employees')->onDelete('cascade');`
- **Line 56**: `'approved_by_inventory',`
- **Line 62**: `$table->uuid('approved_by')->nullable();`
- **Line 63**: `$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');`

### 12. 2025_11_09_053154_create_salary_history_table.php
- **Line 21**: `$table->uuid('approved_by')->nullable();`
- **Line 28**: `$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');`

### 13. 2025_11_09_053149_create_leave_applications_table.php
- **Line 26**: `$table->uuid('approved_by')->nullable();`
- **Line 29**: `$table->uuid('rejected_by')->nullable();`
- **Line 32**: `$table->uuid('cancelled_by')->nullable();`
- **Line 38**: `$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');`
- **Line 39**: `$table->foreign('rejected_by')->references('id')->on('employees')->onDelete('set null');`
- **Line 40**: `$table->foreign('cancelled_by')->references('id')->on('employees')->onDelete('set null');`

### 14. 2025_10_15_055201_create_call_backs_table.php
- **Line 24**: `$table->uuid('reported_by');`
- **Line 25**: `$table->foreign('reported_by')->references('id')->on('employees')->onDelete('restrict');`

### 15. 2025_10_23_100000_create_expiry_confirmations_table.php
- **Line 22**: `$table->uuid('confirmed_by');`
- **Line 23**: `$table->foreign('confirmed_by')->references('id')->on('employees')->onDelete('cascade');`

### 16. 2025_11_14_000005_create_report_templates_table.php
- **Line 30**: `$table->foreignUuid('created_by')->nullable()->constrained('employees')->nullOnDelete();`

### 17. 2025_10_15_053301_create_recipes_table.php
- **Line 30**: `$table->uuid('created_by');`
- **Line 31**: `$table->foreign('created_by')->references('id')->on('employees')->onDelete('restrict');`

### 18. 2025_10_09_012623_create_stock_takes_table.php
- **Line 21**: `$table->uuid('conducted_by');`
- **Line 22**: `$table->foreign('conducted_by')->references('id')->on('employees')->onDelete('restrict');`
- **Line 24**: `$table->uuid('verified_by')->nullable();`
- **Line 25**: `$table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');`

### 19. 2025_10_09_012622_create_item_dispatches_table.php
- **Line 20**: `$table->uuid('dispatched_by');`
- **Line 23**: `$table->foreign('dispatched_by')->references('id')->on('employees')->onDelete('restrict');`
- **Line 24**: `$table->uuid('received_by')->nullable();`
- **Line 25**: `$table->foreign('received_by')->references('id')->on('employees')->onDelete('restrict');`

### 20. 2025_10_20_063309_create_sales_hifts_table.php
- **Line 32**: `$table->uuid('verified_by')->nullable();`
- **Line 33**: `$table->foreign('verified_by')->references('id')->on('employees')->onDelete('set null');`

### 21. 2025_10_20_063352_create_sales_table.php
- **Line 22**: `$table->uuid('sold_by');`
- **Line 23**: `$table->foreign('sold_by')->references('id')->on('employees')->onDelete('restrict');`

### 22. 2025_10_21_073703_create_product_callbacks_table.php
- **Line 22**: `$table->uuid('recorded_by');`
- **Line 23**: `$table->foreign('recorded_by')->references('id')->on('employees')->onDelete('cascade');`

### 23. 2025_11_02_021715_create_product_dispatch_callbacks_table.php
- **Line 30**: `$table->uuid('recorded_by');`
- **Line 31**: `$table->foreign('recorded_by')->references('id')->on('employees')->onDelete('cascade');`
- **Line 51**: `'approved_by_production',`
- **Line 52**: `'received_by_production',`
- **Line 57**: `$table->uuid('approved_by')->nullable();`
- **Line 58**: `$table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');`
- **Line 61**: `$table->uuid('received_by')->nullable();`
- **Line 62**: `$table->foreign('received_by')->references('id')->on('employees')->onDelete('set null');`

### 24. 2025_10_22_094919_create_approved_items_table.php
- **Line 20**: `$table->uuid('approved_by');`

### 25. 2025_10_15_054442_create_production_records_table.php
- **Line 20**: `$table->uuid('produced_by');`
- **Line 21**: `$table->foreign('produced_by')->references('id')->on('employees')->onDelete('restrict');`

---

## Pattern Analysis

### Common "_by" Columns

| Column Name | Count | Most Common Usage |
|------------|-------|-------------------|
| `approved_by` | 8 | Approval tracking |
| `recorded_by` | 4 | Action recording |
| `dispatched_by` | 3 | Dispatch tracking |
| `received_by` | 4 | Receipt tracking |
| `created_by` | 2 | Creation tracking |
| `verified_by` | 2 | Verification tracking |
| `rejected_by` | 2 | Rejection tracking |
| `confirmed_by` | 1 | Confirmation |
| `conducted_by` | 1 | Conduction |
| `reported_by` | 1 | Reporting |
| `sold_by` | 1 | Sales tracking |
| `produced_by` | 1 | Production tracking |
| `moved_by_id/type` | 1 | Morphic relation |
| `checked_by_id/type` | 1 | Morphic relation |
| `requested_by_id/type` | 1 | Morphic relation |
| `cancelled_by` | 2 | Cancellation |
| `acknowledged_by` | 1 | Acknowledgment |
| `allocated_by` | 1 | Allocation |
| `compiled_by` | 1 | Compilation |
| `generated_by` | 1 | Generation |
| `reviewed_by` | 1 | Review |

### Foreign Key Patterns

**Most Common**: References `employees` table
**Delete Strategies**:
- `onDelete('cascade')` - 7 occurrences
- `onDelete('set null')` - 12 occurrences
- `onDelete('restrict')` - 8 occurrences

### Nullable vs Required

- **Nullable** (nullable()): 18 fields
- **Required**: 8 fields
