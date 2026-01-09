# Callback System - Complete Documentation

**Last Updated:** January 8, 2026  
**Status:** 54% Complete (7/13 tasks)  
**Next Action:** Implement Unit & Feature Tests (Phase 1)

---

## 📋 Quick Navigation

| Document | Purpose | Read Time |
|----------|---------|-----------|
| **00_SYSTEM_OVERVIEW.md** | High-level architecture & current status | 10 min |
| **01_ERRORS_AND_ISSUES.md** | Detailed error analysis (11 issues) | 15 min |
| **02_IMPROVEMENTS_AND_SOLUTIONS.md** | How to fix issues with code examples | 20 min |
| **03_IMPLEMENTATION_ROADMAP.md** | Step-by-step implementation guide | 15 min |
| **README.md** | This file - quick reference | 5 min |

---

## 🎯 System Status at a Glance

### ✅ Completed (54%)
- [x] Polymorphic relationships fixed
- [x] Stock update methods implemented
- [x] Automatic stock updates enabled
- [x] Quantity validation added
- [x] Status enum created
- [x] Database indexes added
- [x] N+1 queries verified/optimized

### ⏳ Pending (46%)
- [ ] Unit tests (4 hours)
- [ ] Feature tests (4 hours)
- [ ] Documentation update (2 hours)
- [ ] Event listeners (4 hours) - Optional
- [ ] Status standardization (3 hours) - Optional
- [ ] Legacy table review (2 hours) - Optional

---

## 🚀 Getting Started

### Quick Stats
- **Lines of Code Added:** ~400
- **Files Created:** 2 (enum + migration)
- **Files Modified:** 2 (models)
- **Tests Needed:** 18+ unit tests + 6 feature tests
- **Estimated Time to Complete:** 18-20 hours

### Current Architecture

```
Product Returns Workflow
├─ Inventory Callbacks (ProductDispatchCallback)
│  ├─ Sales creates → Production approves → receives → completes
│  └─ Stock: ProductStock + DailyProduce updated
│
└─ Production Callbacks (ProductionCallback)
   ├─ Raw Materials: Production creates → Inventory approves
   │  └─ Stock: Updates quantity_available & quantity_damaged
   │
   └─ Finished Products: Production creates → Inventory approves
      └─ Stock: Updates DailyProduce.callback_quantity
```

---

## 🔴 Critical Issues (Must Fix Before Deploy)

### 1. **No Unit Tests** (4 hours)
**Impact:** Stock update logic untested  
**Solution:** Create `tests/Unit/Models/` with comprehensive tests  
**See:** `03_IMPLEMENTATION_ROADMAP.md` → Phase 1, Day 1-2

### 2. **No Feature Tests** (4 hours)
**Impact:** Workflows not tested end-to-end  
**Solution:** Create `tests/Feature/Callbacks/` with workflow tests  
**See:** `03_IMPLEMENTATION_ROADMAP.md` → Phase 1, Day 3-4

### 3. **Documentation Incomplete** (2 hours)
**Impact:** Team can't understand system  
**Solution:** Update docs, create API reference, troubleshooting guide  
**See:** `03_IMPLEMENTATION_ROADMAP.md` → Phase 1, Day 5

---

## 🟠 High-Priority Issues (Strongly Recommended)

### 4. **No Event Listeners** (4 hours)
**Impact:** No audit trail for approvals/rejections  
**Solution:** Create events, listeners, and CallbackAudit model  
**See:** `02_IMPROVEMENTS_AND_SOLUTIONS.md` → Improvement #1

### 5. **Status Name Inconsistency** (3 hours)
**Impact:** Unified queries difficult  
**Solution:** Standardize status names across models  
**See:** `02_IMPROVEMENTS_AND_SOLUTIONS.md` → Improvement #4

---

## 📊 Error Analysis Summary

| Issue | Severity | Hours | Blocking | Status |
|-------|----------|-------|----------|--------|
| Missing Unit Tests | CRITICAL | 4 | Deploy | Not Started |
| Missing Feature Tests | CRITICAL | 4 | Deploy | Not Started |
| Missing Event Listeners | HIGH | 4 | Audit | Not Started |
| Status Inconsistency | HIGH | 3 | Reports | Not Started |
| Documentation Incomplete | HIGH | 2 | Knowledge | In Progress |
| Legacy Table Review | MEDIUM | 2 | Clarity | Not Started |
| Orphaned Callbacks Docs | MEDIUM | 1.5 | Edge Cases | Not Started |
| Polymorphic Testing | MEDIUM | 2 | Safety | Not Started |

See `01_ERRORS_AND_ISSUES.md` for full analysis of all 11 issues.

---

## 🛠️ Implementation Plan

### Phase 1: Verification (REQUIRED - 10 hours)
```
✅ Unit Tests (4h)
   - ProductDispatchCallbackTest.php (10 tests)
   - ProductionCallbackTest.php (8 tests)

✅ Feature Tests (4h)
   - ProductDispatchCallbackWorkflowTest.php (3 tests)
   - ProductionCallbackWorkflowTest.php (3 tests)

✅ Documentation (2h)
   - Update progress tracking
   - Create API reference
   - Create troubleshooting guide
```

**Result:** System is verifiable and documented → Safe to deploy

### Phase 2: Production Ready (RECOMMENDED - 8.5 hours)
```
✅ Event Listeners (4h)
   - CallbackApproved, Completed, Rejected events
   - LogCallbackStatusChange listener
   - CallbackAudit model & migration

✅ Status Standardization (3h)
   - Standardize status names
   - Update all queries
   - Test workflows

✅ Documentation (1.5h)
   - Update to reflect standardization
   - Deployment checklist
```

**Result:** System has audit trail and consistency → Production-ready

### Phase 3: Polish (OPTIONAL - 6+ hours)
```
✅ Legacy Table Cleanup (2h)
✅ Performance Optimization (2h)
✅ Integration Testing (2h+)
```

**Result:** System is optimized and mature

---

## 📈 Timeline

```
Week 1: Phase 1 (Verification)
├─ Mon-Tue: Unit Tests (4h)
├─ Wed-Thu: Feature Tests (4h)
└─ Fri: Documentation (2h)
= 10 hours → System deployable

Week 2: Phase 2 (Production Ready)
├─ Mon-Tue: Event Listeners (4h)
├─ Wed-Thu: Status Standardization (3h)
└─ Fri: Documentation + Testing (1.5h)
= 8.5 hours → System production-ready

Week 3: Phase 3 (Polish) - Optional
├─ Mon: Legacy Table Review (2h)
├─ Tue-Wed: Performance Optimization (2h)
└─ Thu-Fri: Integration Testing (2h+)
= 6+ hours → System optimized
```

---

## 🔍 Error Severity Breakdown

### By Severity
- **Critical:** 2 issues (block deployment)
- **High:** 4 issues (strongly recommend fixing)
- **Medium:** 4 issues (nice to have)
- **Low:** 3 issues (future enhancements)

### By Impact
- **Deployment:** 4 issues
- **Data Integrity:** 3 issues
- **Maintainability:** 2 issues
- **Performance:** 2 issues

See `01_ERRORS_AND_ISSUES.md` for detailed breakdown.

---

## 🛣️ Recommended Fix Order

### Mandatory (Before Deploy)
1. Create unit tests
2. Create feature tests
3. Update documentation
4. Run `php artisan test` → All pass
5. Deploy to staging
6. Test in real environment
7. Deploy to production

### After Deploy
1. Add event listeners
2. Standardize status names
3. Optimize performance
4. Review legacy tables

---

## 📚 Model Reference

### ProductDispatchCallback
**Purpose:** Inventory callbacks (Sales → Production)

```php
// Relationships
$callback->product              // Product being returned
$callback->salesShift           // Sales shift
$callback->productDispatch      // Original dispatch (nullable for orphaned)
$callback->recordedBy()         // User/Employee who created
$callback->approvedBy()         // User/Employee who approved
$callback->receivedBy()         // User/Employee who received

// Methods
$callback->approve(actor)                    // Approve callback
$callback->markAsReceived(actor)            // Mark as received
$callback->completeWithStockUpdate()        // Complete & update stock
$callback->validateQuantity()               // Validate qty
```

### ProductionCallback
**Purpose:** Production callbacks (damaged items)

```php
// Relationships
$callback->item                 // Raw material (if applicable)
$callback->product              // Finished product (if applicable)
$callback->shift                // Production shift
$callback->recordedBy()         // User/Employee who created
$callback->approvedBy()         // User/Employee who approved

// Methods
$callback->approve(actor)                    // Approve & update stock
$callback->reject(actor, reason)            // Reject callback
$callback->complete()                       // Mark complete
$callback->validateQuantity()               // Validate qty
$callback->isRawMaterial()                  // Check type
```

### CallbackStatus Enum
**Purpose:** Type-safe status handling

```php
CallbackStatus::PENDING
CallbackStatus::APPROVED_BY_PRODUCTION
CallbackStatus::RECEIVED_BY_PRODUCTION
CallbackStatus::COMPLETED
CallbackStatus::APPROVED_BY_INVENTORY
CallbackStatus::REJECTED

// Methods
$status->canTransitionTo($target)           // Validate transition
$status->getLabel()                         // Human-readable label
$status->getBadgeColor()                    // UI badge color
```

---

## 🧪 Testing Guide

### Unit Tests
Test individual methods in isolation.

```bash
# Run all unit tests
php artisan test tests/Unit/

# Run specific test file
php artisan test tests/Unit/Models/ProductDispatchCallbackTest.php

# Run specific test method
php artisan test tests/Unit/Models/ProductDispatchCallbackTest.php --filter=test_validates_quantity
```

### Feature Tests
Test complete workflows and integrations.

```bash
# Run all feature tests
php artisan test tests/Feature/

# Run callback tests
php artisan test tests/Feature/Callbacks/
```

### Coverage Report
```bash
php artisan test --coverage

# Generate HTML report
php artisan test --coverage --coverage-html=coverage
```

---

## 🐛 Debugging Guide

### Check Stock Updates
```php
// Did stock get updated?
$stock = ProductStock::find(1);
echo $stock->callback_quantity; // Should be 50

// Check audit trail (if event listeners implemented)
$audits = CallbackAudit::where('callback_id', 1)->get();
$audits->each(fn($audit) => echo $audit->action);
```

### Check Polymorphic Relationships
```php
$callback = ProductDispatchCallback::find(1);
echo get_class($callback->recordedBy); // User or Employee?
echo $callback->recordedBy->name;      // Name of actor
```

### Check Status Transitions
```php
$callback = ProductDispatchCallback::find(1);
echo $callback->status;                   // Current status
echo $callback->status->getLabel();       // Human-readable
echo $callback->status->getBadgeColor();  // UI color
```

---

## 🚨 Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| Stock not updated | Missing `completeWithStockUpdate()` call | Use model method, not UI |
| Polymorphic relationship fails | Wrong actor type | Check actor is User or Employee |
| Over-return allowed | Validation not running | Ensure model boot() method active |
| Status transition fails | Invalid state machine | Check `canTransitionTo()` logic |
| N+1 queries | Missing eager loading | Use `->with()` in queries |

See `01_ERRORS_AND_ISSUES.md` for more issues.

---

## 📋 Deployment Checklist

Before deploying to production:

- [ ] All unit tests pass (`php artisan test tests/Unit/`)
- [ ] All feature tests pass (`php artisan test tests/Feature/`)
- [ ] Code coverage >90%
- [ ] Migrations run successfully
- [ ] Stock updates verified in staging
- [ ] Status transitions verified
- [ ] Polymorphic relationships work
- [ ] No N+1 queries detected
- [ ] Documentation updated
- [ ] Logs reviewed for errors
- [ ] Performance acceptable
- [ ] Rollback plan prepared

---

## 🔗 File Structure

```
md/CALL_A_ACK/
├─ README.md                          (this file)
├─ 00_SYSTEM_OVERVIEW.md             (architecture & status)
├─ 01_ERRORS_AND_ISSUES.md           (detailed error analysis)
├─ 02_IMPROVEMENTS_AND_SOLUTIONS.md  (how to fix with code)
└─ 03_IMPLEMENTATION_ROADMAP.md      (step-by-step guide)

app/Models/
├─ ProductDispatchCallback.php        (✅ implemented)
├─ ProductionCallback.php             (✅ implemented)
└─ CallbackAudit.php                 (⏳ to be created)

app/Enums/
└─ CallbackStatus.php                (✅ implemented)

app/Events/
├─ CallbackApproved.php              (⏳ to be created)
├─ CallbackCompleted.php             (⏳ to be created)
└─ CallbackRejected.php              (⏳ to be created)

app/Listeners/
└─ LogCallbackStatusChange.php       (⏳ to be created)

tests/Unit/Models/
├─ ProductDispatchCallbackTest.php   (⏳ to be created)
└─ ProductionCallbackTest.php        (⏳ to be created)

tests/Feature/Callbacks/
├─ ProductDispatchCallbackWorkflowTest.php  (⏳ to be created)
└─ ProductionCallbackWorkflowTest.php       (⏳ to be created)

database/migrations/
├─ 2025_12_07_000002_add_callback_indexes.php          (✅ created)
└─ 2026_01_08_000001_create_callback_audits_table.php (⏳ to be created)
```

---

## 🎓 Learning Path

**New to the system?** Read in this order:

1. **00_SYSTEM_OVERVIEW.md** - Understand what the system does
2. **01_ERRORS_AND_ISSUES.md** - Learn current state and issues
3. **02_IMPROVEMENTS_AND_SOLUTIONS.md** - See how to fix
4. **03_IMPLEMENTATION_ROADMAP.md** - Follow implementation steps

---

## ✅ Next Steps

### Immediate (This Week)
1. Read `00_SYSTEM_OVERVIEW.md`
2. Review `01_ERRORS_AND_ISSUES.md`
3. Follow `03_IMPLEMENTATION_ROADMAP.md` Phase 1

### Short Term (2-3 weeks)
1. Complete Phase 1 (verification)
2. Run migrations
3. Deploy to staging
4. Test in realistic environment
5. Consider Phase 2 (production ready)

### Long Term (1+ month)
1. Phase 2 (event listeners, status standardization)
2. Phase 3 (polish, optimization)
3. Ongoing maintenance and monitoring

---

## 📞 Support & Questions

### Documentation
- System Overview: `00_SYSTEM_OVERVIEW.md`
- Error Details: `01_ERRORS_AND_ISSUES.md`
- Solutions: `02_IMPROVEMENTS_AND_SOLUTIONS.md`
- Implementation: `03_IMPLEMENTATION_ROADMAP.md`

### Code Examples
- Unit tests: `tests/Unit/Models/`
- Feature tests: `tests/Feature/Callbacks/`
- Models: `app/Models/ProductDispatchCallback.php`, `ProductionCallback.php`

### Quick Reference
- All 11 issues documented with severity & solutions
- Step-by-step implementation with code examples
- Complete test files ready to copy-paste
- Deployment checklist included

---

## 📊 Progress Summary

| Category | Status | Completion |
|----------|--------|-----------|
| Core Implementation | ✅ Complete | 100% |
| Unit Tests | ⏳ Pending | 0% |
| Feature Tests | ⏳ Pending | 0% |
| Documentation | 🟡 In Progress | 30% |
| Event Listeners | ⏳ Pending | 0% |
| Status Standardization | ⏳ Pending | 0% |
| **Overall** | **⏳ On Track** | **54%** |

---

## 🎯 Success Criteria

System is **complete** when:

- ✅ All 18+ unit tests pass
- ✅ All 6+ feature tests pass
- ✅ Documentation 100% complete
- ✅ Code coverage >90%
- ✅ Zero test failures
- ✅ Event listeners implemented (optional)
- ✅ Status standardized (optional)
- ✅ Deployed to production
- ✅ No regressions in 2 weeks

---

**Last Updated:** January 8, 2026  
**Next Review:** After Phase 1 completion  
**Maintained By:** Development Team

