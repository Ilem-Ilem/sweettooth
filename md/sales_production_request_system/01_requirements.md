# Sales to Production Request System - Requirements

## Functional Requirements

### 1. Request Creation (Sales Department)

#### Req 1.1: Department Selection
- **Actor**: Sales user
- **Action**: Select target production department
- **Constraints**:
  - Dropdown list of all active production departments
  - Default: Primary production department associated with sales department
  - Show department capacity and current workload

#### Req 1.2: Product Selection
- **Actor**: Sales user
- **Action**: View and select products from chosen department
- **Constraints**:
  - Only show products assigned to selected production department
  - Display product name, SKU, batch size
  - Display current inventory level
  - Allow multiple product selection with batch quantities
  - Validate batch quantities meet minimum requirements

#### Req 1.3: Request Submission
- **Actor**: Sales user
- **Action**: Submit production request
- **Constraints**:
  - Capture request metadata: department, priority, notes
  - Auto-capture: created_by, created_at, sales_department
  - Set initial status: `pending`
  - Validate all required fields
  - Create timestamp for tracking

### 2. Real-time Notifications (Production Department)

#### Req 2.1: Request Notification
- **Actor**: Production staff
- **Action**: Receive notification of new production request
- **Constraints**:
  - Broadcast to all staff in target production department
  - Show request details: products, quantities, priority
  - Include link to request details
  - Mark as read/unread
  - Sound/visual alert for urgent requests

#### Req 2.2: Request Board
- **Actor**: Production staff
- **Action**: View all requests for their department
- **Constraints**:
  - Sort by: priority, created_at, status
  - Filter by: status, priority, created_by
  - Show request details inline
  - Mark as started/completed
  - Add notes/comments

### 3. Production Progress Tracking

#### Req 3.1: Progress Updates
- **Actor**: Production staff
- **Action**: Update production progress
- **Constraints**:
  - Milestones: started, in_production, quality_check, completed
  - Progress percentage (0-100%)
  - Optional notes for each update
  - Timestamp for each milestone
  - Broadcast updates to sales user in real-time

#### Req 3.2: Progress Visibility
- **Actor**: Sales user
- **Action**: View production progress in real-time
- **Constraints**:
  - Live updates without page refresh
  - Show current milestone and progress percentage
  - Display timeline of all updates
  - Show notes from production
  - Estimate completion time

### 4. Dispatch & Verification

#### Req 4.1: Dispatch Creation
- **Actor**: Production staff
- **Action**: Mark request as completed and create dispatch
- **Constraints**:
  - Link dispatch to production request
  - Capture actual quantities produced
  - Create ProductDispatch records for each product
  - Set status: `dispatched`
  - Notify sales user

#### Req 4.2: Verification
- **Actor**: Sales user
- **Action**: Verify and accept/reject dispatched products
- **Constraints**:
  - Review dispatch details
  - Verify quantities match request
  - Inspect batch quality
  - Accept or reject with notes
  - If accepted: close request (status: `accepted`)
  - If rejected: status: `rejected`, send feedback to production

#### Req 4.3: Rejection Workflow
- **Actor**: Sales user / Production staff
- **Action**: Handle rejected dispatch
- **Constraints**:
  - Capture rejection reason
  - Provide feedback to production
  - Auto-create new request or update existing
  - Notify production of rejection
  - Track rejection history

## Non-Functional Requirements

### Performance
- Real-time updates within 2 seconds
- Request list load within 1 second
- Support 100+ concurrent users
- Cache frequently accessed department/product data

### Reliability
- No loss of request data
- Retry failed notifications
- Graceful degradation if WebSocket unavailable
- Audit log all status changes

### Usability
- Mobile-responsive design
- Intuitive navigation
- Clear status indicators
- Helpful error messages
- Minimal clicks to complete actions

### Security
- Only authorized users can create requests
- Only target production department can update
- Only request creator can reject dispatch
- Audit all modifications
- Rate limit API endpoints

## Data Requirements

### Required Fields - ProductionRequest
- `sales_department_id` - Which sales dept created it
- `production_department_id` - Target production dept
- `status` - Current status (enum)
- `priority` - normal/urgent
- `created_by_id` - User who created it
- `created_at` - When created
- `started_at` - When production began
- `completed_at` - When production finished

### Required Fields - ProductionProgressFeedback
- `production_request_id` - Which request
- `milestone` - started/in_production/quality_check/completed
- `progress_percentage` - 0-100
- `notes` - Optional feedback
- `updated_by_id` - Who updated it
- `created_at` - Timestamp

### Required Fields - ProductDispatch
- `production_request_id` - Link to request
- `product_id` - What was produced
- `quantity_produced` - How many made
- `quantity_dispatched` - How many sent
- `status` - pending_verification/accepted/rejected

## Integration Points

### Existing Systems
- **ProductionRequest** - Enhance with new fields/relationships
- **Product/Department** - Filter products by department
- **User/Auth** - Capture user actions
- **ProductDispatch** - Link to requests
- **Broadcasting** - Real-time updates

### External Systems
- None specified at this time
