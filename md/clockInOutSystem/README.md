# 13_MAINTENANCE_OPERATIONS.md

## Maintenance Operations & Support Procedures

**Date**: December 2025
**Version**: 1.0
**Status**: Maintenance Plan Complete

---

## Daily Maintenance Procedures

### Automated Tasks Verification

#### Morning Checklist (8:00 AM)
```bash
#!/bin/bash
# Daily maintenance script - run at 8:00 AM

echo "=== SweetTooth Shift System Daily Maintenance ==="

# 1. Verify Laravel scheduler is running
echo "Checking Laravel scheduler..."
ps aux | grep "artisan schedule:run" | grep -v grep > /dev/null
if [ $? -eq 0 ]; then
    echo "✅ Laravel scheduler is running"
else
    echo "❌ Laravel scheduler is not running - starting..."
    nohup php artisan schedule:run >> /var/log/laravel/scheduler.log 2>&1 &
fi

# 2. Check queue workers
echo "Checking queue workers..."
ps aux | grep "queue:work" | grep -v grep | wc -l | while read count; do
    if [ $count -ge 2 ]; then
        echo "✅ Queue workers running: $count processes"
    else
        echo "❌ Insufficient queue workers: $count - starting additional..."
        nohup php artisan queue:work --queue=shift_notifications >> /var/log/laravel/queue.log 2>&1 &
    fi
done

# 3. Verify auto clock out command ran
echo "Checking auto clock out execution..."
last_run=$(php artisan tinker --execute="echo \Cache::get('auto_clock_out_last_run');")
if [ -n "$last_run" ]; then
    echo "✅ Auto clock out last ran: $last_run"
else
    echo "❌ Auto clock out never ran - manual execution needed"
    php artisan shifts:auto-clock-out
fi

# 4. Check system health
echo "Checking system health..."
health_response=$(curl -s http://localhost/health/shift-system)
if echo "$health_response" | grep -q '"status":"healthy"'; then
    echo "✅ System health: GOOD"
else
    echo "❌ System health issues detected"
    echo "$health_response"
fi

# 5. Verify database backups
echo "Checking database backups..."
if [ -f "/var/backups/sweettooth_$(date +%Y%m%d).sql" ]; then
    echo "✅ Database backup exists for today"
else
    echo "❌ Database backup missing - manual backup needed"
    mysqldump -u sweettooth -p sweettooth > /var/backups/sweettooth_$(date +%Y%m%d).sql
fi

echo "=== Daily maintenance check complete ==="
```

#### Shift Metrics Monitoring
```bash
#!/bin/bash
# Monitor shift system metrics - run every 15 minutes

# Get current metrics
active_shifts=$(php artisan tinker --execute="
\$shifts = \App\Models\Shift::where('status', 'active')->count();
\$violations_today = \App\Models\TimeViolation::whereDate('created_at', \Carbon\Carbon::today())->count();
echo \$shifts . ',' . \$violations_today;
")

IFS=',' read -r active_count violations_count <<< "$active_shifts"

# Alert thresholds
max_active=1000
max_violations=50

# Check active shifts
if [ "$active_count" -gt "$max_active" ]; then
    echo "CRITICAL: High active shifts count: $active_count (threshold: $max_active)"
    # Send alert
    curl -X POST -H 'Content-type: application/json' \
         --data '{"text":"🚨 CRITICAL: '$active_count' active shifts exceed threshold of '$max_active'"}' \
         $SLACK_WEBHOOK_URL
fi

# Check violations
if [ "$violations_count" -gt "$max_violations" ]; then
    echo "WARNING: High time violations today: $violations_count (threshold: $max_violations)"
    # Send alert
    curl -X POST -H 'Content-type: application/json' \
         --data '{"text":"⚠️ WARNING: '$violations_count' time violations today exceed threshold of '$max_violations'"}' \
         $SLACK_WEBHOOK_URL
fi

echo "Active shifts: $active_count | Today's violations: $violations_count"
```

### Manual Daily Tasks

#### 1. Review System Logs (15 minutes)
- Check Laravel logs for errors
- Review shift violation logs
- Monitor auto clock out execution
- Verify notification delivery

#### 2. Database Health Check (10 minutes)
```sql
-- Check for data consistency
SELECT COUNT(*) as orphaned_shifts
FROM shifts s
LEFT JOIN users u ON s.employee_id = u.id
WHERE u.id IS NULL AND s.status = 'active';

-- Check for invalid time ranges
SELECT COUNT(*) as invalid_times
FROM shifts
WHERE clock_out IS NOT NULL
  AND clock_out <= clock_in;

-- Verify shift configurations
SELECT COUNT(*) as missing_configs
FROM shifts s
LEFT JOIN shift_configurations sc ON sc.branch_id = s.branch_id
  AND sc.shift_type = s.shift_type
  AND sc.is_active = 1
WHERE s.status = 'active' AND sc.id IS NULL;
```

#### 3. User Support Review (20 minutes)
- Check support tickets related to clock in/out
- Review user feedback and suggestions
- Update knowledge base with new issues
- Follow up on unresolved issues

---

## Weekly Maintenance Procedures

### Monday Morning Tasks

#### 1. Weekly Metrics Review (30 minutes)
```php
// Generate weekly shift report
$weeklyReport = app(LogAnalyzer::class)->generateWeeklyReport();

// Key metrics to review:
- Total shifts created vs. completed
- Auto clock out percentage
- Time violation trends
- System performance metrics
- User feedback summary
```

#### 2. Database Optimization (45 minutes)
```sql
-- Analyze table statistics
ANALYZE TABLE shifts, shift_configurations, time_violations, shift_notifications;

-- Check for unused indexes
SELECT
    TABLE_NAME,
    INDEX_NAME,
    CARDINALITY,
    PAGES,
    FILTER_CONDITION
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'sweettooth'
  AND SEQ_IN_INDEX = 1
  AND CARDINALITY / TABLE_ROWS < 0.1; -- Indexes with low selectivity

-- Optimize tables if needed
OPTIMIZE TABLE shifts, shift_configurations;

-- Clean up old data (older than 90 days)
DELETE FROM shift_notifications
WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

DELETE FROM time_violations
WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
  AND requires_attention = 0;
```

#### 3. Cache Management (15 minutes)
```bash
# Clear application caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify cache functionality
php artisan tinker --execute="
\Cache::put('test_key', 'test_value', 10);
echo \Cache::get('test_key') === 'test_value' ? '✅ Cache working' : '❌ Cache failed';
\Cache::forget('test_key');
"
```

### Friday Afternoon Tasks

#### 1. Weekend Preparation (30 minutes)
- Verify all scheduled jobs are configured
- Check backup schedules
- Review on-call procedures
- Update contact lists

#### 2. Security Review (20 minutes)
```php
// Check for suspicious patterns
$suspiciousPatterns = DB::table('time_violations')
    ->select('employee_id', 'violation_type', DB::raw('COUNT(*) as count'))
    ->whereDate('created_at', '>=', now()->subDays(7))
    ->groupBy('employee_id', 'violation_type')
    ->having('count', '>', 10) // More than 10 violations per type per week
    ->get();

// Check for unusual login patterns
$unusualLogins = DB::table('time_violations')
    ->where('violation_type', 'too_early')
    ->whereRaw('HOUR(created_at) < 4') // Before 4 AM
    ->whereDate('created_at', '>=', now()->subDays(7))
    ->count();
```

---

## Monthly Maintenance Procedures

### First Monday of Month

#### 1. Comprehensive System Audit (2 hours)
```php
// Run full system audit
$auditResults = [
    'shift_integrity' => $this->auditShiftIntegrity(),
    'configuration_consistency' => $this->auditConfigurationConsistency(),
    'performance_metrics' => $this->auditPerformanceMetrics(),
    'security_compliance' => $this->auditSecurityCompliance(),
    'user_experience' => $this->auditUserExperience()
];

// Generate audit report
$this->generateAuditReport($auditResults);
```

#### 2. Performance Tuning (1 hour)
```sql
-- Identify slow queries
SELECT
    sql_text,
    exec_count,
    avg_timer_wait/1000000000 as avg_time_seconds,
    rows_examined,
    rows_sent
FROM performance_schema.events_statements_summary_by_digest
WHERE schema_name = 'sweettooth'
  AND avg_timer_wait > 1000000000 -- > 1 second
ORDER BY avg_timer_wait DESC
LIMIT 10;

-- Check index usage
SELECT
    object_schema,
    object_name,
    index_name,
    count_read,
    count_fetch,
    count_insert,
    count_update,
    count_delete
FROM performance_schema.table_io_waits_summary_by_index_usage
WHERE object_schema = 'sweettooth'
  AND index_name IS NOT NULL
ORDER BY (count_read + count_fetch) DESC
LIMIT 20;
```

#### 3. Capacity Planning (45 minutes)
- Review growth trends
- Project future resource needs
- Plan infrastructure upgrades
- Update monitoring thresholds

### Last Friday of Month

#### 1. User Feedback Analysis (1 hour)
- Review monthly user satisfaction surveys
- Analyze support ticket trends
- Identify common user issues
- Plan improvements for next month

#### 2. Documentation Updates (45 minutes)
- Update runbooks with new procedures
- Review and update troubleshooting guides
- Add new known issues to knowledge base
- Update training materials

---

## Quarterly Maintenance Procedures

### End of Quarter Reviews

#### 1. Comprehensive Performance Review (4 hours)
- Analyze quarterly metrics trends
- Review system uptime and reliability
- Assess user adoption and satisfaction
- Evaluate business impact and ROI

#### 2. Security Assessment (3 hours)
```php
// Run security audit
$securityAudit = [
    'access_control' => $this->auditAccessControls(),
    'data_encryption' => $this->auditDataEncryption(),
    'audit_trails' => $this->auditTrailIntegrity(),
    'vulnerability_scan' => $this->runVulnerabilityScan(),
    'compliance_check' => $this->checkComplianceStatus()
];
```

#### 3. Disaster Recovery Testing (2 hours)
- Test backup restoration procedures
- Verify failover mechanisms
- Review incident response plans
- Update recovery time objectives

---

## Emergency Maintenance Procedures

### Critical System Issues

#### 1. Auto Clock Out Failure
**Detection**: Health check alerts or user reports
**Response Time**: Within 30 minutes
**Procedure**:
```bash
# 1. Check system status
php artisan shifts:health-check

# 2. Verify scheduler status
ps aux | grep "artisan schedule:run"

# 3. Check recent logs
tail -f /var/log/laravel/scheduler.log

# 4. Manual execution if needed
php artisan shifts:auto-clock-out --force

# 5. Restart services if necessary
sudo systemctl restart laravel-scheduler
sudo systemctl restart laravel-worker
```

#### 2. Database Connectivity Issues
**Detection**: Application errors or health check failures
**Response Time**: Within 15 minutes
**Procedure**:
```bash
# 1. Check database service
sudo systemctl status mysql

# 2. Verify connection
php artisan tinker --execute="DB::connection()->getPdo()"

# 3. Check connection pool
php artisan tinker --execute="DB::select('SELECT 1')"

# 4. Restart application if needed
php artisan cache:clear
sudo systemctl restart php8.3-fpm
```

#### 3. High Violation Rates
**Detection**: Monitoring alerts for excessive violations
**Response Time**: Within 1 hour
**Procedure**:
```php
# 1. Identify violation patterns
php artisan tinker --execute="
\App\Models\TimeViolation::select('violation_type', DB::raw('COUNT(*) as count'))
    ->whereDate('created_at', today())
    ->groupBy('violation_type')
    ->get();
"

# 2. Check system configuration
php artisan config:show shift-monitoring

# 3. Review recent changes
php artisan tinker --execute="
\App\Models\AuditLog::where('action', 'config_update')
    ->where('created_at', '>=', now()->subHours(24))
    ->get();
"
```

### System Outage Procedures

#### 1. Immediate Response (0-5 minutes)
- Alert on-call engineer
- Check system monitoring dashboards
- Initiate incident response protocol
- Notify stakeholders for critical outages

#### 2. Assessment Phase (5-15 minutes)
- Determine scope and impact
- Identify root cause
- Assess recovery options
- Communicate status to affected users

#### 3. Recovery Phase (15-60 minutes)
- Implement fix or workaround
- Restore services incrementally
- Verify system functionality
- Monitor for stability

#### 4. Post-Incident Review (1-4 hours)
- Document incident details
- Identify improvement opportunities
- Update runbooks and procedures
- Implement preventive measures

---

## Backup and Recovery Procedures

### Automated Backups

#### Daily Database Backups
```bash
#!/bin/bash
# /etc/cron.daily/sweettooth-backup

BACKUP_DIR="/var/backups/sweettooth"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump --single-transaction --routines --triggers \
    -u backup_user -p"$DB_PASSWORD" sweettooth \
    > $BACKUP_DIR/sweettooth_db_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/sweettooth_db_$DATE.sql

# Clean old backups (keep 30 days)
find $BACKUP_DIR -name "sweettooth_db_*.sql.gz" -mtime +30 -delete

# Verify backup
if [ $? -eq 0 ]; then
    echo "✅ Database backup completed: sweettooth_db_$DATE.sql.gz"
else
    echo "❌ Database backup failed"
    exit 1
fi
```

#### Configuration Backups
```bash
#!/bin/bash
# Backup application configuration
tar -czf /var/backups/sweettooth/config_$DATE.tar.gz \
    /var/www/sweettooth/.env \
    /var/www/sweettooth/config/ \
    /etc/supervisor/conf.d/sweettooth-*.conf

# Backup shift configurations
php artisan tinker --execute="
\App\Models\ShiftConfiguration::all()->toJson();
" > /var/backups/sweettooth/shift_config_$DATE.json
```

### Recovery Procedures

#### Database Recovery
```bash
#!/bin/bash
# Database recovery script

if [ -z "$1" ]; then
    echo "Usage: $0 <backup_file>"
    exit 1
fi

BACKUP_FILE=$1

echo "⚠️  WARNING: This will overwrite the current database!"
read -p "Are you sure? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Recovery cancelled."
    exit 0
fi

# Extract if compressed
if [[ $BACKUP_FILE == *.gz ]]; then
    gunzip -c $BACKUP_FILE > /tmp/recovery.sql
    BACKUP_FILE=/tmp/recovery.sql
fi

# Restore database
mysql -u root -p sweettooth < $BACKUP_FILE

# Verify restoration
php artisan tinker --execute="
echo 'Shifts: ' . \App\Models\Shift::count();
echo 'Configurations: ' . \App\Models\ShiftConfiguration::count();
"

echo "✅ Database recovery completed"
```

#### Application Recovery
```bash
#!/bin/bash
# Application recovery script

# Restore configuration
tar -xzf /var/backups/sweettooth/config_latest.tar.gz -C /

# Clear and rebuild caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
sudo supervisorctl restart sweettooth-*

echo "✅ Application recovery completed"
```

---

## Monitoring and Alerting Setup

### Nagios/Icinga Integration

```bash
# /etc/nagios/conf.d/sweettooth.cfg

define host {
    use                 linux-server
    host_name           sweettooth-app
    alias               SweetTooth Application
    address             192.168.1.100
}

define service {
    use                 generic-service
    host_name           sweettooth-app
    service_description Shift System Health
    check_command       check_http!-H localhost -u /health/shift-system
}

define service {
    use                 generic-service
    host_name           sweettooth-db
    service_description Database Connectivity
    check_command       check_mysql!-H localhost -d sweettooth
}
```

### Prometheus Metrics

```yaml
# prometheus.yml
scrape_configs:
  - job_name: 'sweettooth-shift-system'
    static_configs:
      - targets: ['localhost:8000']
    metrics_path: '/metrics/shift-system'
    scrape_interval: 30s
```

### Application Metrics Endpoint

```php
// routes/web.php
Route::get('/metrics/shift-system', [MetricsController::class, 'shiftSystem']);

// MetricsController.php
public function shiftSystem()
{
    $metrics = [
        '# HELP shift_system_active_shifts Current number of active shifts',
        '# TYPE shift_system_active_shifts gauge',
        "shift_system_active_shifts " . Shift::where('status', 'active')->count(),

        '# HELP shift_system_time_violations_today Time violations recorded today',
        '# TYPE shift_system_time_violations_today counter',
        "shift_system_time_violations_today " . TimeViolation::whereDate('created_at', today())->count(),

        '# HELP shift_system_auto_clock_out_last_run_seconds Time since last auto clock out run',
        '# TYPE shift_system_auto_clock_out_last_run_seconds gauge',
        "shift_system_auto_clock_out_last_run_seconds " . $this->getSecondsSinceLastAutoClockOut(),

        '# HELP shift_system_queue_size Current shift notification queue size',
        '# TYPE shift_system_queue_size gauge',
        "shift_system_queue_size " . DB::table('jobs')->where('queue', 'shift_notifications')->count(),
    ];

    return response(implode("\n", $metrics), 200, [
        'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8'
    ]);
}
```

---

## Knowledge Base Management

### Documentation Maintenance

#### Monthly Updates
- Review and update runbooks
- Add new troubleshooting procedures
- Update contact information
- Archive outdated procedures

#### Change Management
- Document all system changes
- Update impact assessments
- Maintain change logs
- Review rollback procedures

### Training Materials Updates

#### Quarterly Reviews
- Update video tutorials for new features
- Refresh interactive training modules
- Update quick reference guides
- Review and update FAQs

---

## Compliance and Audit Procedures

### Regular Compliance Checks

#### GDPR Compliance (Monthly)
- Review data retention policies
- Audit user consent records
- Check data encryption status
- Verify access control logs

#### Labor Law Compliance (Quarterly)
- Review working time regulations
- Audit attendance records
- Verify break time compliance
- Check overtime calculations

### Security Audits

#### Penetration Testing (Semi-Annual)
- External security assessment
- Vulnerability scanning
- Code security review
- Infrastructure security audit

---

**Document Information**
- **Prepared By**: Operations & Infrastructure Team
- **Reviewed By**: Security & Compliance Teams
- **Approved By**: Chief Technology Officer
- **Next Review Date**: Quarterly review cycle</content>
<parameter name="filePath">md/clockInOutSystem/13_MAINTENANCE_OPERATIONS.md