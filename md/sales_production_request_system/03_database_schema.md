# Sales to Production Request System - Database Schema

## Table: production_requests (Modified)

### New/Modified Columns

```sql
ALTER TABLE production_requests ADD COLUMN (
  sales_department_id BIGINT UNSIGNED NULL,
  production_department_id BIGINT UNSIGNED NULL,
  status ENUM('pending', 'in_progress', 'quality_check', 'completed', 'dispatched', 'accepted', 'rejected', 'cancelled') DEFAULT 'pending',
  priority ENUM('normal', 'urgent') DEFAULT 'normal',
  created_by_id CHAR(36) NULL,
  started_at TIMESTAMP NULL,
  completed_at TIMESTAMP NULL
);

ALTER TABLE production_requests ADD CONSTRAINT fk_sales_department
  FOREIGN KEY (sales_department_id) REFERENCES departments(id) ON DELETE SET NULL;

ALTER TABLE production_requests ADD CONSTRAINT fk_production_department
  FOREIGN KEY (production_department_id) REFERENCES departments(id) ON DELETE SET NULL;

ALTER TABLE production_requests ADD CONSTRAINT fk_created_by_user
  FOREIGN KEY (created_by_id) REFERENCES users(id) ON DELETE SET NULL;

CREATE INDEX idx_production_request_status ON production_requests(status);
CREATE INDEX idx_production_request_dept ON production_requests(production_department_id);
CREATE INDEX idx_production_request_sales_dept ON production_requests(sales_department_id);
CREATE INDEX idx_production_request_dept_status ON production_requests(production_department_id, status);
CREATE INDEX idx_production_request_created_at ON production_requests(created_at);
```

### Full Schema

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | BIGINT | No | Auto | PK |
| shift_id | BIGINT | Yes | NULL | FK → shifts (existing) |
| item_request_id | BIGINT | Yes | NULL | FK → item_requests (existing) |
| recipe_id | BIGINT | Yes | NULL | FK → recipes (existing) |
| planned_production_quantity | DECIMAL(10,2) | Yes | NULL | How much to make |
| notes | TEXT | Yes | NULL | Production notes |
| **sales_department_id** | BIGINT | Yes | NULL | **NEW** - Which sales dept created |
| **production_department_id** | BIGINT | Yes | NULL | **NEW** - Target production dept |
| **status** | ENUM | No | pending | **NEW** - Current status |
| **priority** | ENUM | No | normal | **NEW** - normal/urgent |
| **created_by_id** | CHAR(36) | Yes | NULL | **NEW** - User who created |
| **started_at** | TIMESTAMP | Yes | NULL | **NEW** - When production began |
| **completed_at** | TIMESTAMP | Yes | NULL | **NEW** - When production finished |
| created_at | TIMESTAMP | No | NOW | (existing) |
| updated_at | TIMESTAMP | No | NOW | (existing) |

---

## Table: production_progress_feedback (New)

### Schema

```sql
CREATE TABLE production_progress_feedback (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  production_request_id BIGINT UNSIGNED NOT NULL,
  milestone ENUM('started', 'in_production', 'quality_check', 'completed') DEFAULT 'in_production',
  progress_percentage INT DEFAULT 0,
  notes TEXT NULL,
  updated_by_id CHAR(36) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_production_request_feedback
    FOREIGN KEY (production_request_id) REFERENCES production_requests(id) ON DELETE CASCADE,
  
  CONSTRAINT fk_updated_by_user
    FOREIGN KEY (updated_by_id) REFERENCES users(id) ON DELETE SET NULL,
  
  INDEX idx_production_request_feedback (production_request_id, created_at),
  INDEX idx_feedback_milestone (milestone),
  INDEX idx_feedback_updated_by (updated_by_id)
);
```

### Columns

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| id | BIGINT | No | Auto | PK |
| production_request_id | BIGINT | No | - | FK → production_requests, cascade delete |
| milestone | ENUM | No | in_production | started/in_production/quality_check/completed |
| progress_percentage | INT | No | 0 | 0-100% |
| notes | TEXT | Yes | NULL | Progress notes/comments |
| updated_by_id | CHAR(36) | Yes | NULL | FK → users, production staff member |
| created_at | TIMESTAMP | No | NOW | When this update was created |
| updated_at | TIMESTAMP | No | NOW | When this record was updated |

---

## Table: product_dispatches (Modified)

### New Column

```sql
ALTER TABLE product_dispatches ADD COLUMN (
  production_request_id BIGINT UNSIGNED NULL
);

ALTER TABLE product_dispatches ADD CONSTRAINT fk_dispatch_production_request
  FOREIGN KEY (production_request_id) REFERENCES production_requests(id) ON DELETE CASCADE;

CREATE INDEX idx_dispatch_production_request ON product_dispatches(production_request_id);
```

### Impact

| Column | Type | Nullable | Default | Notes |
|--------|------|----------|---------|-------|
| **production_request_id** | BIGINT | Yes | NULL | **NEW** - Links dispatch to request |

---

## Relationships Diagram

```
┌─────────────────────────────────┐
│    production_requests          │
├─────────────────────────────────┤
│ id (PK)                         │
│ sales_department_id → depts     │
│ production_department_id → depts│
│ status                          │
│ priority                        │
│ created_by_id → users           │
│ started_at                      │
│ completed_at                    │
└─────────────────────────────────┘
          │
          ├─ 1:N → production_progress_feedback
          │         ├─ milestone
          │         ├─ progress_percentage
          │         └─ updated_by_id → users
          │
          └─ 1:N → product_dispatches
                   ├─ product_id → products
                   ├─ quantity_produced
                   └─ status
```

---

## Query Examples

### Find all pending requests for a production department
```sql
SELECT * FROM production_requests 
WHERE production_department_id = ? 
  AND status = 'pending'
ORDER BY priority DESC, created_at ASC;
```

### Get latest progress for a request
```sql
SELECT * FROM production_progress_feedback 
WHERE production_request_id = ? 
ORDER BY created_at DESC 
LIMIT 1;
```

### Find all requests created by sales user
```sql
SELECT * FROM production_requests 
WHERE sales_department_id = ? 
ORDER BY created_at DESC;
```

### Get requests with active production
```sql
SELECT * FROM production_requests 
WHERE status IN ('pending', 'in_progress', 'quality_check')
ORDER BY priority DESC, created_at ASC;
```

---

## Performance Considerations

### Indexes Added
- `idx_production_request_status` - Filter by status
- `idx_production_request_dept` - Get requests for department
- `idx_production_request_sales_dept` - Get requests created by sales
- `idx_production_request_dept_status` - Combined filter
- `idx_production_request_created_at` - Sort by date
- `idx_production_progress_request` - Get feedback for request
- `idx_dispatch_production_request` - Link dispatch to request

### Optimization Tips
1. Use lazy loading for relationships when listing requests
2. Eager load `progressFeedback` when displaying details
3. Cache department availability status
4. Archive old completed requests after 30 days
5. Use pagination for large lists (20-50 items per page)

---

## Data Integrity Rules

### Constraints
1. `production_request_id` in feedback is NOT NULL (required)
2. `status` must be one of enum values
3. `priority` must be one of enum values
4. `progress_percentage` must be 0-100
5. Foreign keys cascade delete for data consistency

### Business Rules
1. Cannot delete request if dispatch exists
2. Cannot reject dispatch if status != 'dispatched'
3. Cannot accept dispatch if status != 'dispatched'
4. Status transitions must follow workflow
5. Only creator can reject dispatch

---

## Migration Script

See: `database/migrations/2025_12_27_120000_create_sales_to_production_workflow.php`
