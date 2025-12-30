# Sales to Production Request System - Implementation Plan

## Phase 1: Database & Models (Foundation)

### Tasks
1. ✅ Create migration: `2025_12_27_120000_create_sales_to_production_workflow`
   - Add fields to `production_requests` table
   - Create `production_progress_feedback` table
   - Add FK to `product_dispatches`

2. Create/Update Models
   - [ ] Update `ProductionRequest` model with new relationships
   - [ ] Create `ProductionProgressFeedback` model
   - [ ] Update `ProductDispatch` model with FK

3. Create Model Factories & Seeders
   - [ ] Factory for ProductionRequest
   - [ ] Factory for ProductionProgressFeedback
   - [ ] Seeder for test data

**Estimated Time**: 2-3 hours  
**Status**: In Progress

---

## Phase 2: API Endpoints (Backend)

### Controllers to Create/Update

#### ProductionRequestController
```
POST   /api/production-requests              - Create request
GET    /api/production-requests              - List requests (filterable)
GET    /api/production-requests/{id}         - Get request details
PATCH  /api/production-requests/{id}         - Update request
DELETE /api/production-requests/{id}         - Cancel request
GET    /api/production-requests/{id}/progress - Get progress updates
```

#### ProductionRequestDepartmentController
```
GET    /api/production-departments/{id}/requests    - List dept requests
GET    /api/production-departments/{id}/products    - Get available products
```

#### ProductionProgressController
```
POST   /api/production-requests/{id}/progress       - Create progress update
PATCH  /api/production-requests/{id}/progress/{pid} - Update progress update
GET    /api/production-requests/{id}/milestones     - Get all milestones
```

#### DispatchVerificationController
```
GET    /api/production-requests/{id}/dispatch       - Get dispatch details
PATCH  /api/production-requests/{id}/accept-dispatch - Accept dispatch
PATCH  /api/production-requests/{id}/reject-dispatch - Reject dispatch
```

### Validation Rules
- ProductionRequest creation: department, products, quantities
- Progress update: milestone, progress_percentage
- Dispatch verification: rejection notes if rejecting

### Error Handling
- 404 - Request not found
- 403 - Unauthorized (wrong department/user)
- 422 - Validation failed
- 409 - Invalid status transition

**Estimated Time**: 4-5 hours  
**Status**: Not Started

---

## Phase 3: Real-time Broadcasting (WebSockets)

### Events to Create

#### From Production to Sales
- `ProductionRequest\RequestCreated` - New request available
- `ProductionRequest\ProgressUpdated` - Progress milestone reached
- `ProductionRequest\Dispatched` - Ready for verification

#### Broadcasting Channels
- `production-request.{request_id}` - Updates for specific request
- `production-dept.{dept_id}` - New requests for department
- `sales-request.{user_id}` - Updates for user's requests

### Implementation
1. [ ] Create event classes
2. [ ] Update models to broadcast on save
3. [ ] Configure broadcasting driver (Pusher/Redis)
4. [ ] Test with local WebSocket server

**Estimated Time**: 3-4 hours  
**Status**: Not Started

---

## Phase 4: Frontend Components (UI/UX)

### Sales Department Views

#### ProductionRequestForm
- Department selector dropdown
- Product selection with batch quantities
- Priority selector
- Notes textarea
- Submit button with validation

#### ProductionRequestDashboard
- List all requests created by user
- Real-time progress tracking
- Filter by status
- Sort by date/priority

#### DispatchVerificationWidget
- Shows incoming dispatch
- Displays dispatch details
- Accept/Reject buttons
- Notes/feedback textarea

### Production Department Views

#### ProductionRequestBoard
- Real-time list of requests for department
- Sort/filter options
- Click to view details
- "Start Production" button
- "Mark Complete" button

#### ProductionProgressTracker
- Current request details
- Milestone selector
- Progress percentage slider
- Notes textarea
- Save progress button

### Shared Components
- RequestStatusBadge (pending/in_progress/completed/etc)
- ProgressTimeline (shows all milestones)
- DepartmentSelector (dropdown with icons)
- ProductList (filtered by department)

**Estimated Time**: 6-8 hours  
**Status**: Not Started

---

## Phase 5: Integration & Testing

### Unit Tests
- [ ] ProductionRequest model tests
- [ ] API endpoint tests
- [ ] Business logic tests

### Integration Tests
- [ ] Full workflow: create → progress → dispatch → verify
- [ ] Rejection workflow
- [ ] Real-time update delivery

### Manual Testing Checklist
- [ ] Create request (Sales)
- [ ] Receive notification (Production)
- [ ] View request (Production)
- [ ] Update progress (Production)
- [ ] See update in real-time (Sales)
- [ ] Dispatch products (Production)
- [ ] Accept/Reject dispatch (Sales)
- [ ] Test with multiple concurrent users

**Estimated Time**: 3-4 hours  
**Status**: Not Started

---

## Phase 6: Documentation & Deployment

### Documentation
- [ ] API documentation (OpenAPI/Swagger)
- [ ] User guide for Sales
- [ ] User guide for Production
- [ ] Admin configuration guide

### Deployment
- [ ] Database migrations
- [ ] Deploy code changes
- [ ] Configure WebSocket server
- [ ] Seed test data
- [ ] Verify in staging
- [ ] Deploy to production

**Estimated Time**: 2-3 hours  
**Status**: Not Started

---

## Timeline Summary

| Phase | Tasks | Hours | Status |
|-------|-------|-------|--------|
| 1. Database | Models, migrations | 2-3 | In Progress |
| 2. API | Controllers, endpoints | 4-5 | Not Started |
| 3. Broadcasting | Events, channels | 3-4 | Not Started |
| 4. Frontend | Components, UI | 6-8 | Not Started |
| 5. Testing | Tests, verification | 3-4 | Not Started |
| 6. Docs & Deploy | Documentation, deploy | 2-3 | Not Started |
| **TOTAL** | | **20-27 hours** | |

---

## Dependencies & Blockers

### Dependencies
- Phase 1 → Phase 2 (need models first)
- Phase 2 → Phase 3 (need endpoints for events)
- Phase 1,2,3 → Phase 4 (need backend ready)
- Phase 4 → Phase 5 (need UI to test)

### Potential Blockers
- WebSocket/Broadcasting setup complexity
- Product filtering logic (departments → products)
- Real-time update frequency optimization
- Mobile responsive design challenges

---

## Success Criteria

✅ Sales can create requests with department selection  
✅ Production receives requests in real-time  
✅ Production can update progress with milestones  
✅ Sales sees real-time progress updates  
✅ Sales can accept/reject dispatch  
✅ All data persists correctly  
✅ System handles concurrent requests  
✅ No unhandled errors in logs  

---

## Notes

- Start with Phase 1 database setup
- Run migrations before coding models
- Use TDD for Phase 2 API development
- Test WebSocket locally before production
- Consider caching for product/department data
- Plan rollback strategy for production deployment
