# 01 - Employee Appraisal System

## Overview
Implement a comprehensive employee appraisal system that allows managers to conduct regular performance reviews, set goals, track achievements, and provide constructive feedback.

## Current State
- Basic `performance_rating` and `last_performance_review_date` fields exist in User model
- ProbationReview model exists for probationary employees
- No comprehensive appraisal workflow or forms

## Required Features

### 1. Appraisal Templates
- Create customizable appraisal templates
- Different templates for different job roles/departments
- Configurable rating scales (1-5, 1-10, descriptive)
- Weighted criteria sections (Technical Skills, Communication, Leadership, etc.)

### 2. Appraisal Cycles
- Scheduled appraisal periods (Annual, Semi-annual, Quarterly)
- Automatic notifications to managers when appraisals are due
- Configurable appraisal schedules per department/role

### 3. Appraisal Forms
- Manager assessment form with rating scales
- Employee self-assessment form
- 360-degree feedback collection
- Goal setting and achievement tracking
- Development plan creation

### 4. Performance Goals
- Set SMART goals for employees
- Track goal progress throughout the appraisal period
- Link goals to appraisal ratings
- Goal cascading from company to individual level

### 5. Appraisal Workflow
- Draft → Employee Review → Manager Review → HR Review → Finalized
- Approval workflows for each stage
- Email notifications at each step
- Deadline tracking and reminders

### 6. Appraisal Analytics
- Performance trend analysis
- Department-wise performance comparison
- Individual performance history
- Appraisal completion rates

## Database Tables Needed

### 1. `appraisal_templates`
```sql
- id
- name
- description
- department_id
- applicable_roles (JSON)
- sections (JSON) - criteria with weights
- rating_scale (JSON)
- is_active
- created_by
- created_at, updated_at
```

### 2. `appraisal_cycles`
```sql
- id
- name (e.g., "Q4 2024", "Annual 2024")
- start_date
- end_date
- due_date
- status (planned, active, completed)
- department_id
- created_at, updated_at
```

### 3. `appraisals`
```sql
- id
- employee_id
- manager_id
- appraisal_cycle_id
- template_id
- status (draft, self_review, manager_review, hr_review, completed)
- self_assessment_data (JSON)
- manager_assessment_data (JSON)
- hr_assessment_data (JSON)
- final_rating
- final_comments
- development_plan (JSON)
- submitted_at
- completed_at
- created_at, updated_at
```

### 4. `performance_goals`
```sql
- id
- employee_id
- appraisal_id
- title
- description
- category (individual, team, company)
- target_value
- current_value
- unit (percentage, number, currency)
- start_date
- target_date
- status (not_started, in_progress, completed, cancelled)
- created_at, updated_at
```

### 5. `appraisal_feedbacks`
```sql
- id
- appraisal_id
- reviewer_id
- reviewer_type (manager, peer, subordinate, hr)
- feedback_data (JSON)
- submitted_at
- created_at, updated_at
```

## Implementation Steps

### Phase 1: Core Appraisal System
1. Create appraisal templates management
2. Implement basic appraisal form
3. Add appraisal cycle scheduling
4. Create appraisal workflow

### Phase 2: Advanced Features
1. 360-degree feedback system
2. Goal management integration
3. Performance analytics dashboard
4. Mobile-responsive forms

### Phase 3: Integration & Automation
1. Integration with leave system
2. Integration with training system
3. Automated reminders and notifications
4. Performance-based compensation links

## UI Components Needed

### 1. Appraisal Templates Management
- List/create/edit/delete templates
- Template builder with drag-drop sections
- Preview functionality

### 2. Appraisal Cycles Management
- Schedule appraisal periods
- Assign employees to cycles
- Track completion status

### 3. Employee Appraisal Dashboard
- Upcoming appraisals
- Completed appraisals history
- Goal progress tracking
- Performance trend charts

### 4. Manager Appraisal Dashboard
- Team appraisal status
- Pending reviews
- Performance analytics
- Goal approval workflow

### 5. HR Appraisal Dashboard
- Organization-wide analytics
- Calibration sessions
- Performance distribution charts
- Compliance reporting

## Security & Permissions

### Role-Based Access
- **Employees**: View own appraisals, complete self-assessments
- **Managers**: Conduct team appraisals, approve goals
- **HR**: Manage templates, oversee calibration, view all data
- **Super Admin**: Full system configuration

### Data Privacy
- Appraisal data encrypted in database
- Access logging for sensitive operations
- Right to be forgotten compliance
- Data retention policies

## Integration Points

### 1. Existing Systems
- Employee management (link appraisals to employees)
- Leave management (performance-based leave adjustments)
- Training system (development plan recommendations)
- Payroll system (performance-based bonuses)

### 2. External Systems
- HRIS integration APIs
- Performance management software
- Learning management systems
- Compensation planning tools

## Success Metrics

### 1. Adoption Metrics
- Appraisal completion rate (>90%)
- Template utilization rate
- Manager review timeliness

### 2. Quality Metrics
- Average appraisal scores
- Goal achievement rates
- Employee satisfaction scores

### 3. Business Impact
- Performance improvement trends
- Reduced turnover rates
- Increased employee engagement scores</content>
<parameter name="filePath">md/HR/01_employee_appraisal_system.md