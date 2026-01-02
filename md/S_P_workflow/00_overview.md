# Sales & Production Workflow Overview

## Summary

This documentation covers the workflow for **Sales** and **Production** modules, specifically how users are redirected to shift/stock closing when they clock out and how they are prevented from ending their shift without completing the closing process.

## Key Differences

| Aspect | Sales | Production |
|--------|-------|------------|
| **Shift Closing** | Mandatory | Optional |
| **Stock Closing** | Integrated in shift closing | Part of optional shift closing |
| **Clock Out Behavior** | Redirects to shift closing | Directly closes shift |
| **Workflow Enforcement** | `ValidateSalesWorkflow` middleware | No middleware enforcement |
| **Can Skip Closing?** | No | Yes |

## Document Index

| File | Description |
|------|-------------|
| `01_sales_workflow_states.md` | Sales workflow states and transitions |
| `02_sales_clock_out_redirect.md` | How sales clock out redirects to shift closing |
| `03_sales_shift_closing.md` | Sales shift closing (stock + cash reconciliation) |
| `04_production_workflow_states.md` | Production workflow states |
| `05_production_shift_closing.md` | Production shift closing (optional) |
| `06_workflow_enforcement.md` | Middleware and guards preventing bypass |
| `07_key_files_reference.md` | Reference to all key files |

## Visual Workflow

### Sales Module Flow
```
Clock In → Stock Opening → POS → Clock Out → Shift Closing → Completed
    ↓           ↓           ↓        ↓             ↓            ↓
 Shift      Verify      Process   Redirect    Stock/Cash    Shift
 Created    Opening     Sales     to Shift    Reconcile     Closed
            Quantities            Closing
```

### Production Module Flow
```
Clock In → Production Work → Clock Out → Completed
    ↓            ↓              ↓           ↓
 Shift      Batches/        Shift      [Optional:
 Created    Recipes         Closed      Shift Closing]
```
