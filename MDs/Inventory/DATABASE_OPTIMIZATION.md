# Database Optimization - Analytics Performance Guide

## Overview
Performance optimization strategies for analytics queries, indexing improvements, and query efficiency enhancements.

---

## Part 1: Missing Indexes

### Current Issue
Analytics components run expensive queries without proper indexes:

```php
// Current: Slow without indexes
StockMovement::whereBetween('movement_date', [$from, $to])
    ->where('type', 'adjustment')
    ->where('shift', 'morning')
    ->get();
```

### Solution: Add Strategic Indexes

#### Migration: Create Indexes

```php
// database/migrations/xxxx_xx_xx_add_analytics_indexes.php
return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Single column indexes
            $table->index('movement_date');
            $table->index('type');
            $table->index('reference_type');
            $table->index('reference_id');
            $table->index('stock_id');
            $table->index('moved_by_id');
            $table->index('shift');
            $table->index('department_id');
            
            // Composite indexes (for common filter combinations)
            $table->index(['type', 'movement_date']);
            $table->index(['stock_id', 'movement_date']);
            $table->index(['movement_date', 'shift']);
            $table->index(['movement_date', 'department_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['moved_by_id', 'moved_by_type']);
            
            // For range queries (date + other filter)
            $table->index(['movement_date', 'type', 'stock_id']);
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['movement_date']);
            $table->dropIndex(['type']);
            $table->dropIndex(['reference_type']);
            $table->dropIndex(['reference_id']);
            $table->dropIndex(['stock_id']);
            $table->dropIndex(['moved_by_id']);
            $table->dropIndex(['shift']);
            $table->dropIndex(['department_id']);
            
            $table->dropIndex(['type', 'movement_date']);
            $table->dropIndex(['stock_id', 'movement_date']);
            $table->dropIndex(['movement_date', 'shift']);
            $table->dropIndex(['movement_date', 'department_id']);
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropIndex(['moved_by_id', 'moved_by_type']);
            
            $table->dropIndex(['movement_date', 'type', 'stock_id']);
        });
    }
};
```

### Index Strategy Explanation

| Index | Purpose | Query Pattern |
|-------|---------|---------------|
| `movement_date` | Date range filtering | WHERE movement_date BETWEEN |
| `type` | Movement type filtering | WHERE type = 'in' |
| `stock_id` | Item selection filtering | WHERE stock_id = X |
| `moved_by_id` | User activity tracking | WHERE moved_by_id = X |
| `(type, movement_date)` | Type + date filtering | WHERE type = 'in' AND movement_date BETWEEN |
| `(stock_id, movement_date)` | Item trends over time | For getDailyBreakdown() |
| `(movement_date, shift)` | Shift-based analytics | WHERE shift = 'morning' AND movement_date BETWEEN |

---

## Part 2: Query Optimization

### Issue 1: N+1 Query Problem in getAnalyticsSummary()

**Current Code (BAD - 6+ queries):**
```php
private function getAnalyticsSummary($branchId)
{
    $baseQuery = StockMovement::query()
        ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
        ->whereBetween('movement_date', [$dateFrom, $dateTo]);

    // ❌ Clones and executes 6 separate queries
    $stock_in = $baseQuery->clone()->whereIn('type', ['in', 'return'])->sum('quantity');
    $stock_out = $baseQuery->clone()->whereIn('type', ['out', 'damaged'])->sum('quantity');
    // ... 4 more queries
}
```

**Optimized Code (GOOD - 1 query):**
```php
private function getAnalyticsSummary($branchId)
{
    $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
    $dateTo = Carbon::parse($this->dateTo)->endOfDay();

    // ✅ Single query with all aggregations
    $results = StockMovement::query()
        ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
        ->whereBetween('movement_date', [$dateFrom, $dateTo])
        ->selectRaw(
            'SUM(CASE WHEN type IN ("in", "return") THEN quantity ELSE 0 END) as stock_in,
             SUM(CASE WHEN type IN ("out", "damaged", "transfer") THEN quantity ELSE 0 END) as stock_out,
             SUM(CASE WHEN type = "transfer" THEN 1 ELSE 0 END) as transfers,
             COUNT(*) as total_movements,
             SUM(CASE WHEN type = "adjustment" THEN 1 ELSE 0 END) as adjustments,
             ABS(SUM(CASE WHEN type = "damaged" THEN quantity ELSE 0 END)) as damaged'
        )
        ->first();

    return [
        'today' => [...],  // Calculate from today's specific query
        'period' => [
            'stock_in' => $results->stock_in ?? 0,
            'stock_out' => $results->stock_out ?? 0,
            'transfers' => $results->transfers ?? 0,
            'total_movements' => $results->total_movements ?? 0,
            'adjustments' => $results->adjustments ?? 0,
            'damaged' => $results->damaged ?? 0,
        ],
        // ... rest of summary
    ];
}
```

**Impact:**
- Before: 6 queries = ~15ms
- After: 1 query = ~3ms
- **Improvement: 5x faster**

---

### Issue 2: Inefficient Daily Breakdown Query

**Current (Acceptable but can be faster):**
```php
private function getDailyBreakdown($branchId)
{
    return StockMovement::query()
        ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
        ->whereBetween('movement_date', [$dateFrom, $dateTo])
        ->selectRaw(...)
        ->groupBy('date')
        ->limit(7)
        ->get();
}
```

**Optimized (Better execution plan):**
```php
private function getDailyBreakdown($branchId)
{
    return StockMovement::query()
        ->join('stocks', 'stock_movements.stock_id', '=', 'stocks.id')
        ->where('stocks.branch_id', $branchId)
        ->whereBetween('stock_movements.movement_date', [$dateFrom, $dateTo])
        ->selectRaw('DATE(stock_movements.movement_date) as date, ...')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->limit(7)
        ->get();
}
```

**Why Better:**
- Uses join instead of whereHas (better execution plan)
- Avoids subquery
- Indexes used more efficiently

---

### Issue 3: Type Casting Inefficiency

**Current (String comparisons):**
```php
->whereIn('type', ['in', 'return', 'adjustment'])
```

**Better (If type has enum - future):**
```php
// Consider migrating to database enum type
$table->enum('type', ['in', 'out', 'adjustment', 'transfer', 'damaged', 'return']);
```

---

## Part 3: Query Caching Strategy

### Implementation: Cache Analytics Summary

```php
// app/Livewire/BranchDashboard/Analytics/StockMovementAnalytics.php

private function getAnalyticsSummary($branchId)
{
    // Cache key includes date range (5 minute TTL)
    $cacheKey = "analytics:summary:{$branchId}:{$this->dateFrom}:{$this->dateTo}";
    
    return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($branchId) {
        // Complex query only runs if cache misses
        return [/* analytics data */];
    });
}

// Clear cache when data changes
public function updated($property)
{
    if (in_array($property, ['dateFrom', 'dateTo'])) {
        // Clear relevant cache entries
        $branchId = Auth::guard('employees')->user()?->branch_id;
        Cache::forget("analytics:summary:{$branchId}:{$this->dateFrom}:{$this->dateTo}");
    }
    
    $this->resetPage();
}
```

**Benefits:**
- First user: 100ms (query runs)
- Subsequent users (5 min): 5ms (from cache)
- Auto-clear on date change

---

## Part 4: Materialized Views (Advanced)

For heavy usage, consider materialized views for pre-computed analytics:

```sql
-- Create materialized view for daily analytics
CREATE MATERIALIZED VIEW stock_movement_daily_analytics AS
SELECT
    DATE(sm.movement_date) as date,
    s.branch_id,
    sm.type,
    COUNT(*) as movement_count,
    SUM(ABS(sm.quantity)) as total_quantity,
    COUNT(DISTINCT sm.stock_id) as unique_items
FROM stock_movements sm
JOIN stocks s ON sm.stock_id = s.id
WHERE sm.movement_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
GROUP BY date, s.branch_id, sm.type;

-- Create index on materialized view
CREATE INDEX idx_materialized_analytics 
ON stock_movement_daily_analytics(branch_id, date);

-- Refresh view daily (add to scheduler)
REFRESH MATERIALIZED VIEW stock_movement_daily_analytics;
```

**Use in code:**
```php
public function getDailyBreakdown($branchId)
{
    return DB::table('stock_movement_daily_analytics')
        ->where('branch_id', $branchId)
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->orderBy('date', 'desc')
        ->limit(7)
        ->get();
}
```

---

## Part 5: Connection Pooling for Heavy Queries

For very large datasets, implement connection pooling:

```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    // ... existing config ...
    'options' => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='STRICT_TRANS_TABLES'",
    ],
],

// Add for readonly replica (if using):
'mysql_read' => [
    'driver' => 'mysql',
    'host' => env('DB_READ_HOST', '127.0.0.1'),
    // Use for analytics queries only
],
```

---

## Part 6: Pagination Optimization

### Current Issue
```php
// Slow: Calculates total even when not needed
$movements->paginate(15);  // Runs COUNT query
```

### Solution: Use Cursor Pagination for Large Datasets

```php
// Better for analytics tables
$movements = StockMovement::with(['stock.item', 'mover'])
    ->whereHas('stock', fn ($q) => $q->where('branch_id', $branchId))
    ->orderBy('movement_date', 'desc')
    ->cursorPaginate(15);  // No total count
```

**Benefits:**
- No COUNT query (expensive on large tables)
- Better performance
- Still shows next/previous

**When to use:**
- Analytics tables (stock_movements table grows large)
- Real-time feeds
- When total count not needed

---

## Part 7: Batch Operations

For bulk updates (e.g., denormalizing data):

```php
// BAD: N queries
foreach ($stockMovements as $movement) {
    $movement->update(['shift' => $movement->reference->shift]);
}

// GOOD: Single query
StockMovement::query()
    ->where('reference_type', 'App\Models\ItemRequest')
    ->whereNull('shift')
    ->get()
    ->each(function ($movement) {
        $shift = $movement->reference?->shift;
        if ($shift) {
            $movement->update(['shift' => $shift]);
        }
    });

// BETTER: Batch update with raw SQL
DB::statement(
    'UPDATE stock_movements sm
     INNER JOIN item_requests ir ON sm.reference_id = ir.id
     SET sm.shift = ir.shift
     WHERE sm.reference_type = ? AND sm.shift IS NULL',
    ['App\Models\ItemRequest']
);
```

---

## Part 8: Monitoring & Profiling

### Enable Query Logging (Development)

```php
// In development:
DB::listen(function ($query) {
    if ($query->time > 1000) {  // Log queries > 1 second
        Log::warning('Slow query: ' . $query->sql, ['time' => $query->time]);
    }
});
```

### Use Laravel Debugbar

```
composer require barryvdh/laravel-debugbar --dev
```

Provides:
- Query time profiling
- Query count
- Database queries tab

### Check Execution Plans

```sql
EXPLAIN SELECT ... -- Before optimization
EXPLAIN SELECT ... -- After optimization

-- Look for:
-- ✓ Type: ref (using index)
-- ✗ Type: ALL (full table scan)
-- ✓ Rows: low number
-- ✗ Extra: Using filesort (no ORDER BY index)
```

---

## Implementation Checklist

- [ ] Create indexes migration
- [ ] Run migration on production
- [ ] Analyze query plans
- [ ] Implement rawSQL aggregations
- [ ] Update StockMovementAnalytics query
- [ ] Test performance
- [ ] Add cache layer
- [ ] Monitor slow queries
- [ ] Document in code

---

## Performance Targets

| Query | Before | After | Target |
|-------|--------|-------|--------|
| getAnalyticsSummary | 15ms | 3ms | < 5ms |
| getDailyBreakdown | 20ms | 8ms | < 10ms |
| getTopMovedItems | 25ms | 10ms | < 15ms |
| Full page load | 150ms | 50ms | < 100ms |

---

## Monitoring Queries (SQL)

```sql
-- Find slowest queries
SELECT * FROM mysql.slow_log ORDER BY query_time DESC LIMIT 10;

-- Check table sizes
SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE table_schema = 'sweettooth'
ORDER BY size_mb DESC;

-- Check index usage
SELECT * FROM information_schema.STATISTICS
WHERE table_schema = 'sweettooth'
AND table_name = 'stock_movements';
```

---

## Rollback Plan

If indexes cause issues:
1. Revert migration (drops indexes)
2. Queries slower but still work
3. No data loss

---

## Related Documentation

- Production deployment checklist (include index creation)
- Query optimization standards (code style)
- Performance monitoring setup
