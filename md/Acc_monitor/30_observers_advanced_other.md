# Observers for Advanced and Other Features Integration

This guide explains how observers will automate advanced features like project management, capital tracking, and organizational structure.

## Advanced/Other Observers

### 1. ProjectObserver

Manages project lifecycle and reporting.

```php
class ProjectObserver
{
    public function updated(Project $project): void
    {
        if ($project->wasChanged('status') && $project->status === 'completed') {
            $this->finalizeProject($project);
        }
    }

    private function finalizeProject(Project $project): void
    {
        // Generate final reports
        // Archive project data
    }
}
```

### 2. CapitalAccountObserver

Tracks owner contributions and distributions.

```php
class CapitalAccountObserver
{
    public function created(CapitalAccount $account): void
    {
        $this->createInitialContributionEntry($account);
    }

    private function createInitialContributionEntry(CapitalAccount $account): void
    {
        CapitalContribution::create([
            'capital_account_id' => $account->id,
            'contribution_date' => now(),
            'amount' => $account->initial_contribution,
        ]);
    }
}
```

### 3. CapitalContributionObserver

Updates account balances.

```php
class CapitalContributionObserver
{
    public function created(CapitalContribution $contribution): void
    {
        $this->updateAccountBalance($contribution);
        $this->createJournalEntry($contribution);
    }

    private function updateAccountBalance(CapitalContribution $contribution): void
    {
        $contribution->account->increment('current_balance', $contribution->amount);
    }

    private function createJournalEntry(CapitalContribution $contribution): void
    {
        // Debit Cash, Credit Capital
    }
}
```

### 4. CapitalDistributionObserver

Handles owner withdrawals.

```php
class CapitalDistributionObserver
{
    public function created(CapitalDistribution $distribution): void
    {
        $this->updateAccountBalance($distribution);
        $this->createJournalEntry($distribution);
    }

    private function updateAccountBalance(CapitalDistribution $distribution): void
    {
        $distribution->account->decrement('current_balance', $distribution->amount);
    }
}
```

### 5. FolderObserver

Manages organizational hierarchy.

```php
class FolderObserver
{
    public function deleting(Folder $folder): void
    {
        $this->reassignChildren($folder);
        $this->reassignRecords($folder);
    }

    private function reassignChildren(Folder $folder): void
    {
        $folder->children()->update(['parent_id' => $folder->parent_id]);
    }

    private function reassignRecords(Folder $folder): void
    {
        // Move customers/suppliers to parent folder
    }
}
```

## Integration with Time Tracking and Invoicing

### Enhanced SalesInvoiceObserver

```php
class SalesInvoiceObserver
{
    public function created(SalesInvoice $invoice): void
    {
        if ($invoice->project_id) {
            $this->updateProjectRevenue($invoice);
        }
    }

    private function updateProjectRevenue(SalesInvoice $invoice): void
    {
        // Increment project revenue
    }
}
```

### Enhanced TimeEntryObserver

```php
class TimeEntryObserver
{
    public function created(TimeEntry $entry): void
    {
        if ($entry->project_id) {
            $this->updateProjectTime($entry);
        }
    }

    private function updateProjectTime(TimeEntry $entry): void
    {
        $entry->project->increment('total_hours', $entry->hours_worked);
    }
}
```

## Automated Reporting

### Scheduled Project Reports

```php
class GenerateProjectReports extends Command
{
    public function handle()
    {
        $projects = Project::active()->get();
        
        foreach ($projects as $project) {
            $this->generateReport($project);
        }
    }

    private function generateReport(Project $project)
    {
        $data = [
            'revenue' => $project->totalRevenue(),
            'time' => $project->totalTime(),
            'profitability' => $this->calculateProfitability($project),
        ];
        
        // Generate and store report
    }
}
```

## Registration

```php
public function boot()
{
    Project::observe(ProjectObserver::class);
    CapitalAccount::observe(CapitalAccountObserver::class);
    CapitalContribution::observe(CapitalContributionObserver::class);
    CapitalDistribution::observe(CapitalDistributionObserver::class);
    Folder::observe(FolderObserver::class);
}
```

## Benefits

- Automated project tracking and reporting
- Real-time capital account management
- Hierarchical organization maintenance
- Integrated time and revenue tracking

This completes the advanced feature automation for comprehensive business management.