# 01 - Database Schema Design for Production Progress Tracking

## Current Schema Analysis

### Core Production Tables

#### `item_requests`
- **Purpose**: Requests for items from departments
- **Key Fields**:
  - `id`, `branch_id`, `department_id`, `request_number`
  - `requested_by`, `request_date`, `required_date`
  - `status` (enum: pending, approved, partially_approved, dispatched, partially_dispatched, completed, cancelled)
  - `approved_by`, `approved_at`, `notes`

#### `item_request_details`
- **Purpose**: Specific items and quantities in a request
- **Key Fields**:
  - `request_id`, `item_id`, `quantity_requested`
  - `quantity_approved`, `quantity_dispatched`
  - `uom` (unit of measure), `notes`

#### `production_requests`
- **Purpose**: Links item requests to production shifts
- **Key Fields**:
  - `shift_id`, `item_request_id`, `recipe_id`
  - `planned_production_quantity`, `notes`

#### `daily_produces`
- **Purpose**: Daily production records per shift and recipe
- **Key Fields**:
  - `shift_id`, `recipe_id`, `produce_date`
  - `shift_type` (morning/afternoon)
  - `opening_quantity`, `requested_quantity`, `produced_quantity`
  - `sent_out_quantity`, `order_quantity`, `callback_quantity`
  - `closing_quantity`, `expected_closing`, `variance`
  - `status` (enum: in_progress, completed)
  - `notes`

#### `production_records`
- **Purpose**: Detailed batch-level production records
- **Key Fields**:
  - `daily_produce_id`, `recipe_id`, `batch_number`
  - `produced_by_id`, `produced_by_type`
  - `quantity_produced`, `quantity_approved`, `quantity_rejected`
  - `quantity_sent_out`, `quantity_for_order`, `quantity_remaining`
  - `production_time`, `quality_status`, `dispatch_status`
  - `rejection_reason`, `notes`

## Proposed Schema Enhancements

### 1. Enhanced Status Tracking

#### Add to `daily_produces` table:
```sql
ALTER TABLE daily_produces ADD COLUMN progress_percentage DECIMAL(5,2) DEFAULT 0.00;
ALTER TABLE daily_produces ADD COLUMN estimated_completion_time TIMESTAMP NULL;
ALTER TABLE daily_produces ADD COLUMN actual_completion_time TIMESTAMP NULL;
ALTER TABLE daily_produces ADD COLUMN priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium';
ALTER TABLE daily_produces ADD COLUMN assigned_to UUID NULL; -- User ID
```

#### Add to `production_records` table:
```sql
ALTER TABLE production_records ADD COLUMN progress_stage ENUM('started', 'ingredients_collected', 'processing', 'quality_check', 'packaging', 'ready_for_dispatch') DEFAULT 'started';
ALTER TABLE production_records ADD COLUMN stage_start_time TIMESTAMP NULL;
ALTER TABLE production_records ADD COLUMN stage_end_time TIMESTAMP NULL;
ALTER TABLE production_records ADD COLUMN expected_duration_minutes INT NULL;
```

### 2. Progress Milestones Table

#### New table: `production_milestones`
```sql
CREATE TABLE production_milestones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    production_record_id BIGINT UNSIGNED NOT NULL,
    milestone_type ENUM('ingredients_collected', 'production_started', 'quality_check_passed', 'packaging_completed', 'dispatch_ready') NOT NULL,
    achieved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    achieved_by UUID NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (production_record_id) REFERENCES production_records(id) ON DELETE CASCADE,
    INDEX idx_production_record_milestone (production_record_id, milestone_type),
    INDEX idx_achieved_at (achieved_at)
);
```

### 3. Progress Alerts Table

#### New table: `production_progress_alerts`
```sql
CREATE TABLE production_progress_alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    daily_produce_id BIGINT UNSIGNED NOT NULL,
    alert_type ENUM('delay_warning', 'quality_issue', 'resource_shortage', 'completion_milestone', 'bottleneck_detected') NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    message TEXT NOT NULL,
    is_acknowledged BOOLEAN DEFAULT FALSE,
    acknowledged_by UUID NULL,
    acknowledged_at TIMESTAMP NULL,
    auto_resolved BOOLEAN DEFAULT FALSE,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (daily_produce_id) REFERENCES daily_produces(id) ON DELETE CASCADE,
    INDEX idx_alert_type_severity (alert_type, severity),
    INDEX idx_acknowledged (is_acknowledged),
    INDEX idx_created_at (created_at)
);
```

### 4. Department Progress Summary Table

#### New table: `department_progress_summaries`
```sql
CREATE TABLE department_progress_summaries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    shift_id BIGINT UNSIGNED NOT NULL,
    summary_date DATE NOT NULL,
    shift_type ENUM('morning', 'afternoon') NOT NULL,

    -- Overall metrics
    total_items_requested INT DEFAULT 0,
    total_items_completed INT DEFAULT 0,
    total_items_in_progress INT DEFAULT 0,
    total_items_pending INT DEFAULT 0,

    -- Quantity metrics
    total_quantity_requested DECIMAL(12,2) DEFAULT 0.00,
    total_quantity_produced DECIMAL(12,2) DEFAULT 0.00,
    total_quantity_dispatched DECIMAL(12,2) DEFAULT 0.00,

    -- Time metrics
    average_completion_time_minutes DECIMAL(8,2) NULL,
    on_time_completion_percentage DECIMAL(5,2) DEFAULT 0.00,

    -- Quality metrics
    quality_pass_rate DECIMAL(5,2) DEFAULT 0.00,
    total_rejections INT DEFAULT 0,

    -- Alert counts
    active_alerts_count INT DEFAULT 0,
    critical_alerts_count INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE,

    UNIQUE KEY unique_department_shift_date (department_id, shift_id, summary_date),
    INDEX idx_summary_date (summary_date),
    INDEX idx_department_date (department_id, summary_date)
);
```

### 5. Progress Audit Trail

#### New table: `production_progress_audits`
```sql
CREATE TABLE production_progress_audits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auditable_type VARCHAR(255) NOT NULL, -- daily_produces, production_records, etc.
    auditable_id BIGINT UNSIGNED NOT NULL,
    event_type ENUM('status_changed', 'quantity_updated', 'milestone_achieved', 'alert_created', 'alert_acknowledged') NOT NULL,
    old_value JSON NULL,
    new_value JSON NULL,
    changed_by UUID NOT NULL,
    change_reason TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_auditable (auditable_type, auditable_id),
    INDEX idx_event_type (event_type),
    INDEX idx_changed_by (changed_by),
    INDEX idx_created_at (created_at)
);
```

## Migration Strategy

### Phase 1: Core Enhancements (High Priority)
1. Add progress tracking fields to existing tables
2. Create production_milestones table
3. Create production_progress_alerts table

### Phase 2: Analytics & Reporting (Medium Priority)
1. Create department_progress_summaries table
2. Create production_progress_audits table

### Phase 3: Advanced Features (Low Priority)
1. Add machine learning predictions table
2. Add progress templates table
3. Add automated workflow triggers table

## Data Integrity Considerations

### Foreign Key Constraints
- All foreign keys should use `ON DELETE CASCADE` for progress-related tables
- Consider `ON DELETE SET NULL` for optional references

### Indexes
- Composite indexes on frequently queried combinations
- Date-based indexes for time-range queries
- Status-based indexes for filtering

### Data Validation
- Progress percentage must be between 0-100
- Completion times must be after start times
- Quantities cannot be negative

## Performance Considerations

### Query Optimization
- Use database views for complex progress calculations
- Implement materialized views for real-time dashboards
- Cache frequently accessed progress summaries

### Archival Strategy
- Archive old progress data after 2 years
- Maintain summary tables for historical reporting
- Implement data partitioning by date for large tables