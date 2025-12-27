# 04 - Recruitment Management System

## Overview
Implement a comprehensive recruitment management system that streamlines the hiring process from job posting to onboarding, including applicant tracking, interview scheduling, offer management, and recruitment analytics.

## Current State
- Basic job posting functionality may exist
- No applicant tracking system (ATS)
- Manual interview scheduling and feedback collection
- No recruitment pipeline analytics
- Limited integration with existing HR systems

## Required Features

### 1. Job Requisition Management
- Job requisition approval workflow
- Job description templates and standardization
- Budget approval and headcount management
- Requisition tracking and status updates

### 2. Applicant Tracking System (ATS)
- Multi-channel job posting (internal, external, job boards)
- Resume parsing and candidate profiling
- Application status tracking and communication
- Duplicate candidate detection and merging

### 3. Interview Management
- Interview scheduling and calendar integration
- Interview panel management and coordination
- Structured interview guides and evaluation forms
- Interview feedback collection and rating systems

### 4. Offer Management
- Offer letter generation and templates
- Offer approval workflows
- Counter-offer tracking and negotiation
- Offer acceptance and decline management

### 5. Onboarding Integration
- Pre-employment checks and background verification
- New hire paperwork and document collection
- Onboarding task assignment and tracking
- Welcome kit generation and delivery

### 6. Recruitment Analytics
- Time-to-hire metrics and trends
- Source effectiveness analysis
- Cost-per-hire calculations
- Diversity and inclusion reporting

## Database Tables Needed

### 1. `job_requisitions`
```sql
- id
- title
- department_id
- location
- employment_type (full_time, part_time, contract, internship)
- salary_range_min
- salary_range_max
- currency
- budget_code
- headcount_requested
- job_description
- requirements (JSON)
- responsibilities (JSON)
- benefits (JSON)
- status (draft, pending_approval, approved, open, filled, cancelled)
- priority (low, medium, high, urgent)
- requester_id
- approver_id
- approved_at
- target_fill_date
- created_at, updated_at
```

### 2. `job_postings`
```sql
- id
- requisition_id
- platform (internal, linkedin, indeed, company_website, etc.)
- posting_url
- posted_date
- expiry_date
- status (draft, posted, expired, closed)
- views_count
- applications_count
- created_at, updated_at
```

### 3. `job_applications`
```sql
- id
- job_requisition_id
- applicant_email
- first_name
- last_name
- phone
- resume_file_path
- cover_letter
- source (job_board, referral, website, agency)
- referrer_id (for internal referrals)
- application_date
- status (new, screening, phone_screen, interview, offer, hired, rejected)
- current_stage
- rating (1-5)
- notes
- created_at, updated_at
```

### 4. `interview_schedules`
```sql
- id
- application_id
- interview_round
- interview_type (phone, video, in_person)
- scheduled_date
- start_time
- end_time
- interviewer_ids (JSON)
- location_room
- meeting_link
- status (scheduled, confirmed, completed, cancelled, no_show)
- feedback_due_date
- created_at, updated_at
```

### 5. `interview_feedbacks`
```sql
- id
- interview_schedule_id
- interviewer_id
- rating_overall
- rating_technical
- rating_communication
- rating_cultural_fit
- strengths (JSON)
- weaknesses (JSON)
- recommendation (strong_hire, hire, no_hire, strong_no_hire)
- comments
- submitted_at
- created_at, updated_at
```

### 6. `job_offers`
```sql
- id
- application_id
- offer_date
- offered_salary
- offered_bonus
- offered_benefits (JSON)
- start_date
- offer_letter_path
- status (draft, sent, accepted, declined, countered, withdrawn)
- acceptance_deadline
- accepted_at
- declined_reason
- created_at, updated_at
```

### 7. `candidate_background_checks`
```sql
- id
- application_id
- check_type (criminal, credit, reference, drug_test, education)
- vendor_name
- request_date
- completion_date
- status (pending, in_progress, completed, failed)
- result (clear, concerns, adverse)
- report_path
- notes
- created_at, updated_at
```

### 8. `recruitment_analytics`
```sql
- id
- metric_type (time_to_hire, cost_per_hire, offer_acceptance_rate, etc.)
- department_id
- period_start
- period_end
- value
- benchmark_value
- created_at, updated_at
```

## Implementation Steps

### Phase 1: Core ATS Functionality
1. Job requisition and approval workflow
2. Basic applicant tracking and status management
3. Resume upload and parsing
4. Email integration for automated communications

### Phase 2: Interview & Evaluation
1. Interview scheduling system
2. Interview feedback collection
3. Structured evaluation forms
4. Interview panel coordination

### Phase 3: Offer & Onboarding
1. Offer management and templates
2. Background check integration
3. Onboarding task automation
4. Welcome process management

### Phase 4: Analytics & Optimization
1. Recruitment dashboard and metrics
2. Source effectiveness analysis
3. Predictive analytics for hiring success
4. Diversity and inclusion tracking

## UI Components Needed

### 1. Job Requisition Portal
- Requisition creation form with templates
- Approval workflow visualization
- Budget and headcount tracking
- Requisition status dashboard

### 2. Applicant Tracking Dashboard
- Job-specific applicant lists
- Status update workflows
- Bulk actions for candidate management
- Advanced filtering and search

### 3. Interview Management Center
- Calendar integration for scheduling
- Interview panel management
- Feedback collection forms
- Interview analytics and trends

### 4. Candidate Experience Portal
- Job application forms
- Application status tracking
- Interview feedback sharing
- Offer management interface

### 5. Recruitment Analytics Dashboard
- Time-to-fill metrics
- Source performance analysis
- Cost analysis and ROI
- Diversity hiring reports

### 6. Onboarding Management
- Pre-employment checklist
- Document collection workflow
- Task assignment and tracking
- Onboarding progress monitoring

## Security & Permissions

### Role-Based Access
- **Recruiters/HR**: Full access to ATS, scheduling, and candidate data
- **Hiring Managers**: View assigned requisitions, schedule interviews, provide feedback
- **Interviewers**: Access to scheduled interviews and feedback forms
- **Employees**: Limited access for internal job postings and referrals
- **Candidates**: Secure portal for application status and communications

### Data Privacy
- Candidate PII data encrypted and access-controlled
- GDPR/CCPA compliance for data retention and deletion
- Background check data securely segregated
- Audit trails for all candidate interactions

## Integration Points

### 1. Existing Systems
- **Employee Management**: New hire data integration, headcount tracking
- **Performance Management**: Historical performance data for internal candidates
- **Training System**: Skills assessment integration for job matching
- **Payroll System**: Salary data integration for offer management

### 2. External Systems
- **Job Boards**: Automated posting to Indeed, LinkedIn, Glassdoor
- **Background Check Vendors**: Integration with Checkr, Sterling, etc.
- **Video Interview Platforms**: Zoom, Interviewing.io integration
- **Assessment Tools**: Skills testing and personality assessments
- **Applicant Tracking Systems**: Migration from existing ATS if applicable

## Success Metrics

### 1. Efficiency Metrics
- Time-to-hire reduction (>20% improvement)
- Cost-per-hire optimization
- Offer acceptance rate (>80%)
- Application-to-hire ratio improvement

### 2. Quality Metrics
- Quality-of-hire ratings from managers
- New hire retention rates (>85% at 1 year)
- Performance ratings of new hires
- Diversity hiring goals achievement

### 3. Experience Metrics
- Candidate experience satisfaction scores
- Interviewer satisfaction with tools
- Internal stakeholder satisfaction
- System adoption and usage rates