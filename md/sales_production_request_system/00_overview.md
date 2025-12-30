# Sales to Production Request System - Overview

## Purpose
Establish a real-time, bidirectional communication system between Sales and Production departments for managing product requests, tracking production progress, and verifying completed orders.

## Key Objectives
1. **Sales Department** - Request products from production with department selection
2. **Production Department** - Receive requests in real-time and track them
3. **Real-time Feedback** - Production provides progress updates visible to Sales
4. **Verification Workflow** - Sales verifies and accepts/rejects dispatched products
5. **Department-based Products** - Show available products filtered by production department

## System Participants
- **Sales Department** - Creates production requests
- **Production Department** - Receives and fulfills requests
- **Sales User** - Initiates requests and verifies dispatch
- **Production Staff** - Works on requests and provides updates
- **System** - Broadcasts real-time updates via WebSocket

## Key Features
✅ Department selection for production requests  
✅ Product filtering by department  
✅ Real-time request notifications  
✅ Live progress tracking  
✅ Dispatch verification workflow  
✅ Notes/feedback system  
✅ Request history and analytics  

## Document Structure
- **00_overview.md** (this file) - High-level system overview
- **01_requirements.md** - Detailed requirements and specifications
- **02_implementation_plan.md** - Step-by-step implementation guide
- **03_api_endpoints.md** - API documentation
- **04_database_schema.md** - Database structure and relationships
- **05_real_time_architecture.md** - WebSocket and broadcasting setup
- **06_frontend_components.md** - UI component specifications
- **07_workflow_status_codes.md** - Status definitions and transitions
