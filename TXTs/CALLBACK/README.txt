Callback System Documentation
Complete documentation for the production, sales, and inventory callback system in SweetTooth.
📚 Documentation Files
01SYSTEMOVERVIEW.md - START HERE
   - High-level architecture
   - Three callback types explained
   - System flow and relationships
   - Connection points between departments
   - Read this first to understand the big picture
02MODELANALYSIS.md - TECHNICAL DETAILS
   - Complete model documentation
   - Fillable properties, casts, relationships
   - All methods and scopes
   - Database migrations breakdown
   - Issues and inconsistencies found
   - Detailed technical reference
03WORKFLOWDETAILS.md - HOW IT WORKS
   - Step-by-step workflows for each callback type
   - Visual process flows
   - Implementation details from Livewire components
   - Stock impact calculations
   - Status progression examples
   - Learn how each workflow operates
04ISSUESANDINCONSISTENCIES.md - WHAT'S WRONG
   - 8 critical issues identified
   - Severity and impact assessment
   - Error scenarios and edge cases
   - Missing features
   - Data quality issues
   - Performance problems
   - Understand all current system problems
05CRITICALIMPROVEMENTS.md - HOW TO FIX
   - Priority 1-6 improvements
   - Detailed implementation guides
   - Code examples with solutions
   - Database migration strategies
   - Testing checklist
   - Timeline for implementation
   - Follow this to implement fixes
06QUICKREFERENCE.md - QUICK LOOKUP
   - File structure
   - Database table schemas
   - Status flows
   - Key methods reference
   - Common queries
   - Debugging commands
   - Use this for quick lookups
---
🎯 Quick Navigation
If you need to...
| Task | Go To |
|------|-------|
| Understand the system | 01SYSTEMOVERVIEW.md |
| Find model details | 02MODELANALYSIS.md |
| See how workflows work | 03WORKFLOWDETAILS.md |
| Find what's broken | 04ISSUESANDINCONSISTENCIES.md |
| Fix something | 05CRITICALIMPROVEMENTS.md |
| Look up syntax/schemas | 06QUICKREFERENCE.md |
---
🚨 Critical Issues Summary
3 Blocking Issues (Must Fix First)
Polymorphic Type Mismatch (BLOCKING)
   - Migrations create polymorphic columns but models don't handle them
   - May cause data insertion failures
   - Fix: Update models to use morphTo() OR simplify migrations
   - Effort: 4 hours
   - See: 04ISSUES (Issue #1), 05CRITICALIMPROVEMENTS (Priority 1)
Stock Logic in UI Components (BLOCKING)
   - Business logic scattered in Livewire files
   - Can't be called from API or jobs
   - Difficult to test
   - Fix: Move all stock logic to model methods
   - Effort: 8 hours
   - See: 04ISSUES (Issue #4), 05CRITICALIMPROVEMENTS (Priority 2)
No Automatic Stock Updates in ProductionCallback (BLOCKING)
   - Stock updates require manual handling
   - Not called if approval done outside Livewire
   - Fix: Add approveWithStockUpdate() method
   - Effort: 4 hours
   - See: 04ISSUES (Issue #5), 05CRITICALIMPROVEMENTS (Priority 2)
5 High-Priority Issues (Should Fix Soon)
Duplicate createdBy() Method - Easy to fix (15 min)
Orphaned Callbacks Possible - Needs documentation (2 hours)
Missing Quantity Validation - Add to all components (2 hours)
Missing Branch Index - Performance issue (1 hour)
Inconsistent Status Names - Refactor for consistency (3 hours)
---
📊 System Architecture
PRODUCTION              SALES               INVENTORY
    │                    │                      │
    ├─ Shift             ├─ SalesShift         ├─ Stock
    ├─ DailyProduce      ├─ ProductStock      ├─ StockMovement
    └─ ProductDispatch   └─ Dispatch Callback │
                                               │
                    ProductionCallback ────────┘
                    
    ↓
    Callbacks Track Issues & Returns
    ↓
    Status: pending → approved → received → completed
    ↓
    Automatic Stock Updates (WHEN FIXED)
---
📋 Callback Types
ProductDispatchCallback (Sales → Production)
Purpose: Return products from sales to production
Flow: pending → approvedbyproduction → receivedbyproduction → completed
Approval: Production department
Reasons: expired, damaged, qualityissue, customerreturn, overreceived, wrongitem, other
ProductionCallback (Production → Inventory)
Purpose: Report damaged/defective items
Types: Raw material OR Finished product
Flow: pending → approvedbyinventory → completed (or rejected)
Approval: Inventory department
Reasons: damage, expired, quality, contamination, etc.
ProductCallback (LEGACY)
Status: Not actively used
Action: Should be reviewed for deprecation
---
🔧 Implementation Status
| Feature | Status | Notes |
|---------|--------|-------|
| Created | ✅ | All three callback types implemented |
| Approval Workflow | ✅ | Multi-stage approvals working |
| Employee Tracking | ✅ | Who created/approved tracked |
| Reason Tracking | ✅ | Enum-based reasons captured |
| Status Transitions | ✅ | Proper state machine |
| Stock Updates | ⚠️ | Works but in UI, not model |
| Validation | ⚠️ | Partial - missing some checks |
| Polymorphic Types | ⚠️ | Mismatch between schema & model |
| Audit Trail | ❌ | No history of changes |
| API Support | ⚠️ | Stock logic won't work |
| Queue Support | ❌ | Stock logic tied to UI |
| Database Indexes | ⚠️ | Basic indexes present |
---
📈 Implementation Timeline
Recommended Schedule:
Week 1: Fix polymorphic types + move stock logic (12 hours)
Week 2: Add validation + status enums (7 hours)
Week 3: Add event listeners + indexes (6 hours)
Week 4: Testing + documentation (8 hours)
Total: ~33 hours
---
🧪 Testing Checklist
Before deploying changes:
[ ] Stock updates work via model
[ ] Validation prevents invalid data
[ ] Status transitions enforced
[ ] Callbacks can't be over-approved
[ ] Audit trail captures changes
[ ] APIs work with new methods
[ ] Livewire components updated
[ ] Unit tests pass
[ ] Feature tests pass
[ ] No data integrity issues
[ ] Performance improved
[ ] No regressions
---
📞 Quick Reference Commands
bash
Check pending approvals
php artisan tinker
>>> ProductDispatchCallback::pending()->count()
Get specific type
>>> ProductionCallback::rawMaterial()->get()
Check with relationships
>>> ProductDispatchCallback::with('productDispatch', 'recordedBy')->first()
Get statistics
>>> ProductDispatchCallback::selectRaw('status, COUNT() as count')->groupBy('status')->get()
---
🔍 Finding Issues
Stock not updating?
→ See 04ISSUES (Issue #4, #5), check if callback is 'completed'
Callback not appearing?
→ Check branchid filtering in 06QUICKREFERENCE
Polymorphic error?
→ See 04ISSUES (Issue #1), 02MODELANALYSIS (Database section)
Validation failed unexpectedly?
→ See 04ISSUES (Issue #6), check ProductDispatchCallback.getAvailableQuantity()
---
📝 Key Takeaways
What's Working
✅ Multi-stage workflows
✅ Employee tracking
✅ Status management
✅ Detailed reason tracking
✅ UI components are well-designed
What Needs Fixing
⚠️ Polymorphic type mismatch
⚠️ Stock logic in UI layer
⚠️ No automatic updates
⚠️ Missing validation
⚠️ No audit trail
⚠️ Performance optimization
Key Lesson
Business logic should be in models, not UI components.
Stock updates, validation, and status transitions belong in model methods so they work consistently across all interfaces (Livewire, API, jobs, etc.).
---
📚 Related Documentation
MDs/APPROVALWORKFLOW - Main approval system docs
app/Models/Product.php - Model implementations
database/migrations/ - Schema definitions
resources/views/livewire/branch-dashboard/ - UI implementations
---
🎓 Learning Path
For New Developers:
Read 01SYSTEMOVERVIEW.md (understand the "what")
Read 03WORKFLOWDETAILS.md (understand the "how")
Read 02MODELANALYSIS.md (understand the "code")
Reference 06QUICKREFERENCE.md (for lookups)
For Fixing Bugs:
Check 04ISSUESANDINCONSISTENCIES.md (find it)
Check 05CRITICALIMPROVEMENTS.md (fix it)
Add tests and update docs
For Performance/Architecture:
Read 02MODELANALYSIS.md (current state)
Read 04ISSUESANDINCONSISTENCIES.md (problems)
Read 05CRITICALIMPROVEMENTS.md (solutions)
Implement and test
---
✅ Documentation Checklist
[x] System overview with architecture diagrams
[x] Complete model documentation
[x] Detailed workflow documentation
[x] Issues identified and documented
[x] Solutions and improvements outlined
[x] Quick reference guide
[x] Code examples provided
[x] Testing checklist created
[x] Implementation timeline provided
[x] Navigation and cross-references
---
📞 Questions?
Refer to the specific documentation file:
"What is this system?" → 01SYSTEMOVERVIEW.md
"How do I use it?" → 03WORKFLOWDETAILS.md
"How is it coded?" → 02MODELANALYSIS.md
"What's broken?" → 04ISSUESANDINCONSISTENCIES.md
"How do I fix it?" → 05CRITICALIMPROVEMENTS.md
"Where do I find X?" → 06QUICKREFERENCE.md
---
Last Updated: Dec 5, 2025
Documentation Version: 1.0
System Version: Production (with issues)
Status: Ready for improvement implementation
