# 00 - Advanced RBAC System Overview

## 🎯 Problem Statement

SweetTooth currently has a solid foundation with **Spatie Laravel Permission** for role-based access control, but lacks **contextual scoping**. The current system provides answers to:

- *What can you do?* (via permissions)
- *Who can do it?* (via roles)

But it's missing critical business context:

- *Where can you do it?* (branch restrictions)
- *In which department context?* (departmental boundaries)
- *Within which business module?* (category isolation)

## 🧠 Core Concept

**Add a domain layer above Spatie** that introduces **Category → Department → Role scoping** without breaking Spatie's internals.

### Current System (Flat)
```
User → Role → Permissions
       ↓
   Branch Access
```

### Enhanced System (Hierarchical)
```
User → Department Role Assignment → Role → Permissions
         ↓                              ↓
     Department Context           Spatie Core
         ↓
     Category Module
         ↓
     Branch Boundary
```

## ✅ Key Design Principles

### 1. **Don't Break Spatie**
- Keep all existing Spatie tables and logic intact
- Add domain constraints as a **wrapper layer**
- Maintain backward compatibility

### 2. **Contextual Authorization**
- **Super Admin**: Unrestricted access to everything
- **Admin**: Limited to one branch, multiple departments
- **Department Staff**: Limited to their department's module

### 3. **Business Logic Separation**
- **Spatie**: Handles *what* permissions exist
- **Domain Layer**: Handles *where and when* permissions apply

## 🏗 System Architecture

### Core Components

1. **Categories** - Top-level business modules (HR, Sales, Production, Inventory)
2. **Departments** - Operational units within categories
3. **Role Context Mapping** - Which roles are valid in which departments
4. **User Department Assignments** - Where users actually work

### Access Control Flow

```
Request → Middleware → Category Check → Department Check → Spatie Permission Check → Allow/Deny
```

## 📊 Business Value

### Before Enhancement
- User with "Cashier" role can access ALL sales functions across ALL departments
- No way to restrict "Kitchen Staff" to only kitchen operations
- Branch access is enforced, but departmental boundaries don't exist

### After Enhancement
- "POS Cashier" role only works in POS department
- "Kitchen Staff" role only works in Kitchen department
- "Gelato Staff" role only works in Gelato department
- Cross-department access requires explicit assignment

## 🔄 Migration Strategy

### Phase 1: Database Extensions
- Add new tables without breaking existing data
- Create migration scripts for categories, departments, mappings

### Phase 2: Model Extensions
- Extend User model with department relationships
- Add context-aware authorization methods

### Phase 3: Middleware Updates
- Add department context validation
- Update existing branch middleware

### Phase 4: UI Updates
- Role assignment interface with department selection
- Department-aware navigation

## 🛡️ Security Benefits

### 1. **Principle of Least Privilege**
- Users only access what's needed for their specific department
- No accidental cross-department data access

### 2. **Audit Trail**
- All role assignments include department context
- Clear accountability for who can access what where

### 3. **Business Logic Enforcement**
- HR staff can't accidentally access production data
- Sales staff can't modify inventory records
- Department managers stay within their scope

## 📋 Implementation Roadmap

1. **00 - Overview** (This document)
2. **01 - Database Design** (Tables, relationships, migrations)
3. **02 - Models & Relationships** (Eloquent models, methods)
4. **03 - Implementation** (Seeders, middleware, policies)

## 🎯 Success Criteria

- ✅ Zero breaking changes to existing Spatie implementation
- ✅ Super Admin bypasses all restrictions
- ✅ Department staff limited to their assigned departments
- ✅ Admin can manage multiple departments within their branch
- ✅ Full audit trail of all access decisions
- ✅ Backward compatibility with existing user assignments

## 🚀 Next Steps

Proceed to [01 - Database Design](./01-database-design.md) to understand the table structure and relationships.</content>
<parameter name="filePath">md/advance_RBAS/00-overview.md