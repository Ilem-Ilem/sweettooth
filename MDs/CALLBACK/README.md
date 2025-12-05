# Callback System Documentation

Complete documentation for the production, sales, and inventory callback system in SweetTooth.

## 📚 Documentation Files

### 1. **[01_SYSTEM_OVERVIEW.md](01_SYSTEM_OVERVIEW.md)** - START HERE
   - High-level architecture
   - Three callback types explained
   - System flow and relationships
   - Connection points between departments
   - **Read this first to understand the big picture**

### 2. **[02_MODEL_ANALYSIS.md](02_MODEL_ANALYSIS.md)** - TECHNICAL DETAILS
   - Complete model documentation
   - Fillable properties, casts, relationships
   - All methods and scopes
   - Database migrations breakdown
   - Issues and inconsistencies found
   - **Detailed technical reference**

### 3. **[03_WORKFLOW_DETAILS.md](03_WORKFLOW_DETAILS.md)** - HOW IT WORKS
   - Step-by-step workflows for each callback type
   - Visual process flows
   - Implementation details from Livewire components
   - Stock impact calculations
   - Status progression examples
   - **Learn how each workflow operates**

### 4. **[04_ISSUES_AND_INCONSISTENCIES.md](04_ISSUES_AND_INCONSISTENCIES.md)** - WHAT'S WRONG
   - 8 critical issues identified
   - Severity and impact assessment
   - Error scenarios and edge cases
   - Missing features
   - Data quality issues
   - Performance problems
   - **Understand all current system problems**

### 5. **[05_CRITICAL_IMPROVEMENTS.md](05_CRITICAL_IMPROVEMENTS.md)** - HOW TO FIX
   - Priority 1-6 improvements
   - Detailed implementation guides
   - Code examples with solutions
   - Database migration strategies
   - Testing checklist
   - Timeline for implementation
   - **Follow this to implement fixes**

### 6. **[06_QUICK_REFERENCE.md](06_QUICK_REFERENCE.md)** - QUICK LOOKUP
   - File structure
   - Database table schemas
   - Status flows
   - Key methods reference
   - Common queries
   - Debugging commands
   - **Use this for quick lookups**

---

## 🎯 Quick Navigation

**If you need to...**

| Task | Go To |
|------|-------|
| Understand the system | 01_SYSTEM_OVERVIEW.md |
| Find model details | 02_MODEL_ANALYSIS.md |
| See how workflows work | 03_WORKFLOW_DETAILS.md |
| Find what's broken | 04_ISSUES_AND_INCONSISTENCIES.md |
| Fix something | 05_CRITICAL_IMPROVEMENTS.md |
| Look up syntax/schemas | 06_QUICK_REFERENCE.md |

---

## 🚨 Critical Issues Summary

### 3 Blocking Issues (Must Fix First)

1. **Polymorphic Type Mismatch** (BLOCKING)
   - Migrations create polymorphic columns but models don't handle them
   - May cause data insertion failures
   - **Fix: Update models to use morphTo() OR simplify migrations**
   - Effort: 4 hours
   - See: 04_ISSUES (Issue #1), 05_CRITICAL_IMPROVEMENTS (Priority 1)

2. **Stock Logic in UI Components** (BLOCKING)
   - Business logic scattered in Livewire files
   - Can't be called from API or jobs
   - Difficult to test
   - **Fix: Move all stock logic to model methods**
   - Effort: 8 hours
   - See: 04_ISSUES (Issue #4), 05_CRITICAL_IMPROVEMENTS (Priority 2)

3. **No Automatic Stock Updates in ProductionCallback** (BLOCKING)
   - Stock updates require manual handling
   - Not called if approval done outside Livewire
   - **Fix: Add approveWithStockUpdate() method**
   - Effort: 4 hours
   - See: 04_ISSUES (Issue #5), 05_CRITICAL_IMPROVEMENTS (Priority 2)

### 5 High-Priority Issues (Should Fix Soon)

1. **Duplicate createdBy() Method** - Easy to fix (15 min)
2. **Orphaned Callbacks Possible** - Needs documentation (2 hours)
3. **Missing Quantity Validation** - Add to all components (2 hours)
4. **Missing Branch Index** - Performance issue (1 hour)
5. **Inconsistent Status Names** - Refactor for consistency (3 hours)

---

## 📊 System Architecture

```
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
```

---

## 📋 Callback Types

### 1. ProductDispatchCallback (Sales → Production)
- **Purpose**: Return products from sales to production
- **Flow**: pending → approved_by_production → received_by_production → completed
- **Approval**: Production department
- **Reasons**: expired, damaged, quality_issue, customer_return, over_received, wrong_item, other

### 2. ProductionCallback (Production → Inventory)
- **Purpose**: Report damaged/defective items
- **Types**: Raw material OR Finished product
- **Flow**: pending → approved_by_inventory → completed (or rejected)
- **Approval**: Inventory department
- **Reasons**: damage, expired, quality, contamination, etc.

### 3. ProductCallback (LEGACY)
- **Status**: Not actively used
- **Action**: Should be reviewed for deprecation

---

## 🔧 Implementation Status

| Feature | Status | Notes |
|---------|--------|-------|
| **Created** | ✅ | All three callback types implemented |
| **Approval Workflow** | ✅ | Multi-stage approvals working |
| **Employee Tracking** | ✅ | Who created/approved tracked |
| **Reason Tracking** | ✅ | Enum-based reasons captured |
| **Status Transitions** | ✅ | Proper state machine |
| **Stock Updates** | ⚠️ | Works but in UI, not model |
| **Validation** | ⚠️ | Partial - missing some checks |
| **Polymorphic Types** | ⚠️ | Mismatch between schema & model |
| **Audit Trail** | ❌ | No history of changes |
| **API Support** | ⚠️ | Stock logic won't work |
| **Queue Support** | ❌ | Stock logic tied to UI |
| **Database Indexes** | ⚠️ | Basic indexes present |

---

## 📈 Implementation Timeline

**Recommended Schedule**:

- **Week 1**: Fix polymorphic types + move stock logic (12 hours)
- **Week 2**: Add validation + status enums (7 hours)
- **Week 3**: Add event listeners + indexes (6 hours)
- **Week 4**: Testing + documentation (8 hours)

**Total**: ~33 hours

---

## 🧪 Testing Checklist

Before deploying changes:

- [ ] Stock updates work via model
- [ ] Validation prevents invalid data
- [ ] Status transitions enforced
- [ ] Callbacks can't be over-approved
- [ ] Audit trail captures changes
- [ ] APIs work with new methods
- [ ] Livewire components updated
- [ ] Unit tests pass
- [ ] Feature tests pass
- [ ] No data integrity issues
- [ ] Performance improved
- [ ] No regressions

---

## 📞 Quick Reference Commands

```bash
# Check pending approvals
php artisan tinker
>>> ProductDispatchCallback::pending()->count()

# Get specific type
>>> ProductionCallback::rawMaterial()->get()

# Check with relationships
>>> ProductDispatchCallback::with('productDispatch', 'recordedBy')->first()

# Get statistics
>>> ProductDispatchCallback::selectRaw('status, COUNT(*) as count')->groupBy('status')->get()
```

---

## 🔍 Finding Issues

**Stock not updating?**
→ See 04_ISSUES (Issue #4, #5), check if callback is 'completed'

**Callback not appearing?**
→ Check branch_id filtering in 06_QUICK_REFERENCE

**Polymorphic error?**
→ See 04_ISSUES (Issue #1), 02_MODEL_ANALYSIS (Database section)

**Validation failed unexpectedly?**
→ See 04_ISSUES (Issue #6), check ProductDispatchCallback.getAvailableQuantity()

---

## 📝 Key Takeaways

### What's Working
✅ Multi-stage workflows
✅ Employee tracking
✅ Status management
✅ Detailed reason tracking
✅ UI components are well-designed

### What Needs Fixing
⚠️ Polymorphic type mismatch
⚠️ Stock logic in UI layer
⚠️ No automatic updates
⚠️ Missing validation
⚠️ No audit trail
⚠️ Performance optimization

### Key Lesson
**Business logic should be in models, not UI components.**

Stock updates, validation, and status transitions belong in model methods so they work consistently across all interfaces (Livewire, API, jobs, etc.).

---

## 📚 Related Documentation

- **MDs/APPROVAL_WORKFLOW_*** - Main approval system docs
- **app/Models/Product*.php** - Model implementations
- **database/migrations/** - Schema definitions
- **resources/views/livewire/branch-dashboard/** - UI implementations

---

## 🎓 Learning Path

**For New Developers:**

1. Read 01_SYSTEM_OVERVIEW.md (understand the "what")
2. Read 03_WORKFLOW_DETAILS.md (understand the "how")
3. Read 02_MODEL_ANALYSIS.md (understand the "code")
4. Reference 06_QUICK_REFERENCE.md (for lookups)

**For Fixing Bugs:**

1. Check 04_ISSUES_AND_INCONSISTENCIES.md (find it)
2. Check 05_CRITICAL_IMPROVEMENTS.md (fix it)
3. Add tests and update docs

**For Performance/Architecture:**

1. Read 02_MODEL_ANALYSIS.md (current state)
2. Read 04_ISSUES_AND_INCONSISTENCIES.md (problems)
3. Read 05_CRITICAL_IMPROVEMENTS.md (solutions)
4. Implement and test

---

## ✅ Documentation Checklist

- [x] System overview with architecture diagrams
- [x] Complete model documentation
- [x] Detailed workflow documentation
- [x] Issues identified and documented
- [x] Solutions and improvements outlined
- [x] Quick reference guide
- [x] Code examples provided
- [x] Testing checklist created
- [x] Implementation timeline provided
- [x] Navigation and cross-references

---

## 📞 Questions?

Refer to the specific documentation file:

- **"What is this system?"** → 01_SYSTEM_OVERVIEW.md
- **"How do I use it?"** → 03_WORKFLOW_DETAILS.md
- **"How is it coded?"** → 02_MODEL_ANALYSIS.md
- **"What's broken?"** → 04_ISSUES_AND_INCONSISTENCIES.md
- **"How do I fix it?"** → 05_CRITICAL_IMPROVEMENTS.md
- **"Where do I find X?"** → 06_QUICK_REFERENCE.md

---

**Last Updated**: Dec 5, 2025
**Documentation Version**: 1.0
**System Version**: Production (with issues)
**Status**: Ready for improvement implementation
