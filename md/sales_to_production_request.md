# Sales to Production Request System

## Overview
Functional system for managing production requests from Sales to Production departments with real-time feedback and verification workflow.

## System Flow

### 1. Sales Initiates Request
- Sales user selects products needed for orders
- Chooses target production department
- Sets batch quantities for each product
- System shows products filtered by department
- Default: shows products from the active sales department's primary production partner

### 2. Production Receives Request (Real-time)
- Production staff see incoming requests via dashboard/notification
- Live updates using Laravel Broadcasting (WebSocket/polling)
- Status: `pending` - awaiting production start
- Can view:
  - Product details
  - Batch quantities
  - Expected timeline
  - Priority level

### 3. Production Starts Work
- Production updates status to `in_progress`
- Creates/updates ProductionRecord for tracking
- Sales receives real-time feedback via production progress system
- Progress milestones tracked:
  - `started`
  - `in_production`
  - `quality_check`
  - `completed`

### 4. Production Completes & Dispatches
- Production marks request as `completed`
- Creates ProductDispatch record linking products to request
- Sales receives notification
- Status: `ready_for_dispatch`

### 5. Sales Verifies & Accepts
- Sales reviews dispatched products
- Verifies batches/quantities match request
- Accepts or rejects dispatch
- If accepted: request closes
- If rejected: feedback sent to production

## Database Tables Involved

### ProductionRequest (Existing)
- `id` - PK
- `sales_department_id` - requesting department
- `production_department_id` - target department (NEW)
- `status` - pending, in_progress, completed, dispatched, accepted
- `priority` - normal, urgent
- `created_by_id` - sales user

### ProductionRecord (Existing)
- Tracks actual production work
- Linked to ProductionRequest (NEW FK)
- Progress milestones via `ProductionMilestone` table

### ProductDispatch (Existing)
- Links completed production to sales
- `production_request_id` (NEW FK)
- Status tracking for verification

### ProductionProgressFeedback (NEW)
- Real-time updates from production
- `production_request_id`
- `status` - started, in_production, quality_check, completed
- `progress_percentage`
- `notes`
- `updated_at` - triggers real-time broadcast

## API Endpoints

### Sales Department
```
POST   /api/production-requests          - Create request
GET    /api/production-requests          - List requests
GET    /api/production-requests/{id}     - View request details
GET    /api/production-requests/{id}/dispatch - View dispatch status
PATCH  /api/production-requests/{id}/accept-dispatch - Accept dispatch
PATCH  /api/production-requests/{id}/reject-dispatch - Reject with feedback
```

### Production Department
```
GET    /api/production-requests/department/{dept_id}  - List dept requests
GET    /api/production-requests/{id}/feedback          - Get request
PATCH  /api/production-requests/{id}/start             - Mark in_progress
PATCH  /api/production-requests/{id}/progress          - Update progress
PATCH  /api/production-requests/{id}/complete          - Mark completed
```

## Real-time Broadcasting Channels

### `production-request.{request_id}`
- Events: `RequestCreated`, `ProgressUpdated`, `CompletedDispatch`
- Subscribers: Production dept + Sales user who created

### `production-dept.{dept_id}`
- Events: `NewRequest`
- Subscribers: All production staff in department

## Frontend Components

### Sales
- `ProductionRequestForm` - Create request with dept selection
- `ProductionRequestDashboard` - Track requests + real-time progress
- `DispatchVerification` - Review and accept/reject dispatch

### Production
- `ProductionRequestBoard` - Real-time request queue
- `ProductionProgressTracker` - Update progress
- `RequestCompletion` - Mark done and dispatch

## Features

### 1. Department Selection
- Dropdown of production departments
- Default: Primary production partner for sales dept
- Filters available products by department
- Shows estimated production time per dept

### 2. Real-time Updates
- WebSocket notifications for new requests (production)
- Live progress tracking (sales)
- Instant dispatch notifications
- Chat/notes system for clarifications

### 3. Product Filtering
- Shows only products assigned to selected department
- Displays inventory/stock info
- Shows batch sizes and preparation requirements
- Estimated production time per product

### 4. Verification Workflow
- Sales verifies product quantities match request
- Can inspect batch quality
- Accept/reject with notes
- Rejected items return to production queue

## Status Codes

### ProductionRequest Statuses
- `pending` - awaiting production to start
- `in_progress` - production started
- `quality_check` - undergoing QC
- `completed` - production finished
- `dispatched` - sent to sales
- `accepted` - sales verified and accepted
- `rejected` - sales rejected, feedback sent
- `cancelled` - request cancelled

### Progress Milestones
- `started` - production begins
- `in_production` - actively being made
- `quality_check` - QC phase
- `completed` - finished production
- `dispatched` - sent to sales for pickup/delivery
