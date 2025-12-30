# Sales to Production Request System - API Endpoints

## Base URL
```
/api/v1/production-requests
```

---

## 1. Create Production Request

### Endpoint
```
POST /api/v1/production-requests
```

### Request Body
```json
{
  "production_department_id": 2,
  "products": [
    {
      "product_id": 5,
      "batch_quantity": 10,
      "notes": "Extra vanilla flavoring"
    },
    {
      "product_id": 8,
      "batch_quantity": 15,
      "notes": ""
    }
  ],
  "priority": "normal",
  "notes": "Needed for tomorrow's orders",
  "sales_department_id": 1
}
```

### Response
```json
{
  "id": 42,
  "sales_department_id": 1,
  "production_department_id": 2,
  "status": "pending",
  "priority": "normal",
  "created_by_id": "abc-123-def",
  "created_at": "2025-12-27T10:30:00Z",
  "notes": "Needed for tomorrow's orders",
  "products": [
    {
      "product_id": 5,
      "product_name": "Butter Croissant",
      "batch_quantity": 10
    },
    {
      "product_id": 8,
      "product_name": "Chocolate Chip Cookie",
      "batch_quantity": 15
    }
  ]
}
```

### Status Codes
- `201 Created` - Request created successfully
- `400 Bad Request` - Missing required fields
- `403 Forbidden` - User not authorized
- `422 Unprocessable Entity` - Validation failed

### Validation Rules
- `production_department_id` - Required, must exist
- `products` - Required, at least 1 product
- `products[].product_id` - Required, must exist
- `products[].batch_quantity` - Required, must be > 0
- `priority` - Optional, default 'normal'

---

## 2. List Production Requests

### Endpoint
```
GET /api/v1/production-requests
```

### Query Parameters
```
?status=pending,in_progress
&department=2
&priority=urgent
&created_by=abc-123-def
&sort=created_at
&order=desc
&page=1
&per_page=20
```

### Response
```json
{
  "data": [
    {
      "id": 42,
      "production_department_id": 2,
      "status": "pending",
      "priority": "normal",
      "products_count": 2,
      "created_at": "2025-12-27T10:30:00Z",
      "latest_progress": {
        "milestone": "started",
        "progress_percentage": 25,
        "updated_at": "2025-12-27T11:00:00Z"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 42,
    "last_page": 3
  }
}
```

### Status Codes
- `200 OK` - Request list retrieved
- `401 Unauthorized` - Not authenticated
- `403 Forbidden` - Not authorized to view

---

## 3. Get Request Details

### Endpoint
```
GET /api/v1/production-requests/{id}
```

### Response
```json
{
  "id": 42,
  "sales_department_id": 1,
  "production_department_id": 2,
  "status": "in_progress",
  "priority": "normal",
  "created_by": {
    "id": "abc-123-def",
    "name": "John Sales"
  },
  "created_at": "2025-12-27T10:30:00Z",
  "started_at": "2025-12-27T11:00:00Z",
  "completed_at": null,
  "notes": "Needed for tomorrow's orders",
  "products": [
    {
      "product_id": 5,
      "product_name": "Butter Croissant",
      "batch_quantity": 10,
      "notes": "Extra vanilla flavoring"
    }
  ],
  "progress_milestones": [
    {
      "id": 1,
      "milestone": "started",
      "progress_percentage": 0,
      "updated_by": "Jane Production",
      "created_at": "2025-12-27T11:00:00Z",
      "notes": "Starting batch 1"
    },
    {
      "id": 2,
      "milestone": "in_production",
      "progress_percentage": 25,
      "updated_by": "Jane Production",
      "created_at": "2025-12-27T11:30:00Z",
      "notes": "Mixing ingredients"
    }
  ],
  "dispatches": [
    {
      "id": 1,
      "product_id": 5,
      "quantity_produced": 10,
      "quantity_dispatched": 10,
      "status": "pending_verification"
    }
  ]
}
```

### Status Codes
- `200 OK` - Details retrieved
- `404 Not Found` - Request doesn't exist
- `403 Forbidden` - Not authorized to view

---

## 4. Update Progress

### Endpoint
```
POST /api/v1/production-requests/{id}/progress
```

### Request Body
```json
{
  "milestone": "in_production",
  "progress_percentage": 50,
  "notes": "Halfway through baking"
}
```

### Response
```json
{
  "id": 1,
  "milestone": "in_production",
  "progress_percentage": 50,
  "notes": "Halfway through baking",
  "updated_by_id": "xyz-789-uvw",
  "created_at": "2025-12-27T11:30:00Z"
}
```

### Status Codes
- `201 Created` - Progress update created
- `400 Bad Request` - Invalid milestone
- `403 Forbidden` - Only production dept can update
- `404 Not Found` - Request doesn't exist

### Validation Rules
- `milestone` - Required, one of: started, in_production, quality_check, completed
- `progress_percentage` - Required, 0-100
- `notes` - Optional

---

## 5. Mark Request as Complete

### Endpoint
```
PATCH /api/v1/production-requests/{id}/complete
```

### Request Body
```json
{
  "notes": "Production complete, ready for dispatch"
}
```

### Response
```json
{
  "id": 42,
  "status": "completed",
  "completed_at": "2025-12-27T14:00:00Z",
  "notes": "Production complete, ready for dispatch"
}
```

### Status Codes
- `200 OK` - Marked complete
- `403 Forbidden` - Only production dept can complete
- `409 Conflict` - Invalid status transition

---

## 6. Dispatch Products

### Endpoint
```
POST /api/v1/production-requests/{id}/dispatch
```

### Request Body
```json
{
  "products": [
    {
      "product_id": 5,
      "quantity_produced": 10,
      "quantity_dispatched": 10
    }
  ],
  "notes": "All products packaged and ready"
}
```

### Response
```json
{
  "id": 42,
  "status": "dispatched",
  "dispatch_created_at": "2025-12-27T14:30:00Z",
  "dispatches": [
    {
      "id": 1,
      "product_id": 5,
      "product_name": "Butter Croissant",
      "quantity_produced": 10,
      "quantity_dispatched": 10,
      "status": "pending_verification"
    }
  ]
}
```

### Status Codes
- `201 Created` - Dispatch created
- `400 Bad Request` - Invalid quantities
- `403 Forbidden` - Only production dept can dispatch
- `404 Not Found` - Request doesn't exist

---

## 7. Accept Dispatch

### Endpoint
```
PATCH /api/v1/production-requests/{id}/accept-dispatch
```

### Request Body
```json
{
  "notes": "All products verified and accepted"
}
```

### Response
```json
{
  "id": 42,
  "status": "accepted",
  "accepted_at": "2025-12-27T15:00:00Z",
  "notes": "All products verified and accepted"
}
```

### Status Codes
- `200 OK` - Dispatch accepted
- `403 Forbidden` - Only request creator can accept
- `409 Conflict` - Request not in dispatched status

---

## 8. Reject Dispatch

### Endpoint
```
PATCH /api/v1/production-requests/{id}/reject-dispatch
```

### Request Body
```json
{
  "reason": "Batch size mismatch",
  "notes": "Expected 10 but received 8",
  "reorder": true
}
```

### Response
```json
{
  "id": 42,
  "status": "rejected",
  "rejected_at": "2025-12-27T15:30:00Z",
  "reason": "Batch size mismatch",
  "notes": "Expected 10 but received 8",
  "feedback_sent_to": "production-dept@company.com"
}
```

### Status Codes
- `200 OK` - Dispatch rejected
- `403 Forbidden` - Only request creator can reject
- `409 Conflict` - Request not in dispatched status

---

## 9. Get Department Products

### Endpoint
```
GET /api/v1/production-requests/departments/{id}/products
```

### Response
```json
{
  "department_id": 2,
  "department_name": "Bakery",
  "products": [
    {
      "id": 5,
      "name": "Butter Croissant",
      "sku": "CROISSANT-BTR",
      "batch_size": 10,
      "preparation_time": "45 minutes",
      "assigned_staff": ["Jane Production", "Bob Baker"]
    },
    {
      "id": 8,
      "name": "Chocolate Chip Cookie",
      "sku": "COOKIE-CHOCO",
      "batch_size": 20,
      "preparation_time": "30 minutes",
      "assigned_staff": ["Jane Production"]
    }
  ]
}
```

### Status Codes
- `200 OK` - Products list retrieved
- `404 Not Found` - Department doesn't exist

---

## 10. Get Department Requests

### Endpoint
```
GET /api/v1/production-requests/departments/{id}/requests
```

### Query Parameters
```
?status=pending,in_progress
&sort=created_at
&order=desc
&page=1
&per_page=20
```

### Response
```json
{
  "department_id": 2,
  "requests": [
    {
      "id": 42,
      "status": "pending",
      "priority": "normal",
      "created_at": "2025-12-27T10:30:00Z",
      "products_count": 2,
      "created_by": "John Sales"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 10,
    "last_page": 1
  }
}
```

### Status Codes
- `200 OK` - Requests list retrieved
- `404 Not Found` - Department doesn't exist

---

## Authentication

All endpoints require authentication header:
```
Authorization: Bearer {token}
```

---

## Rate Limiting

- Authenticated users: 100 requests/minute
- Unauthenticated: 10 requests/minute

---

## Error Response Format

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": [
      {
        "field": "production_department_id",
        "message": "Department not found"
      }
    ]
  }
}
```

---

## WebSocket Events

In addition to REST endpoints, real-time updates are broadcast via WebSocket:

### Channels
- `production-request.{request_id}` - Updates for specific request
- `production-dept.{dept_id}` - New requests for department

### Events
- `RequestCreated` - New request available
- `ProgressUpdated` - Progress milestone reached
- `Dispatched` - Ready for verification
- `Accepted` - Request accepted
- `Rejected` - Request rejected
