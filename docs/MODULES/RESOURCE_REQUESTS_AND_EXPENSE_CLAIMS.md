# Resource Requests and Expense Claims Module

**Date:** December 17, 2025  
**Module:** HRM - Resource Requests & Expense Claims  
**Status:** ✅ Implemented

## Overview

Two new modules have been implemented to enhance employee experience and operational efficiency:

1. **Resource Request System** - Allows staff to request items (office supplies, equipment, pantry items, etc.)
2. **Expense Claims System** - Allows staff to claim reimbursement for out-of-pocket expenses (travel, meals, etc.)

---

## 📦 Resource Request System

### Purpose

Enable staff to formally request resources and items they need for work. Admins can review, approve, and fulfill these requests.

### Features

#### For Staff

-   Submit requests for various items (coffee, keyboard, tissue, office supplies, equipment, etc.)
-   Specify quantity, priority, and reason for the request
-   Track request status (pending, approved, fulfilled, rejected)
-   Receive notifications on status changes

#### For Admins

-   View all resource requests with filtering options
-   Review request details including reason and estimated cost
-   Approve or reject requests with notes
-   Mark approved requests as fulfilled
-   Track actual costs and vendors
-   Dashboard with statistics (pending, approved, fulfilled, rejected)

### Database Schema

**Table:** `hrm_resource_requests`

| Field             | Type      | Description                                                      |
| ----------------- | --------- | ---------------------------------------------------------------- |
| id                | bigint    | Primary key                                                      |
| employee_id       | bigint    | FK to hrm_employees                                              |
| item_name         | string    | Name of requested item                                           |
| description       | text      | Detailed description                                             |
| quantity          | integer   | Number of items needed                                           |
| priority          | enum      | low, medium, high, urgent                                        |
| category          | enum      | office_supplies, equipment, pantry, furniture, technology, other |
| status            | enum      | pending, approved, rejected, fulfilled, cancelled                |
| reason            | text      | Why the item is needed                                           |
| approved_by       | bigint    | FK to users                                                      |
| approved_by_name  | string    | Name of approver                                                 |
| approved_at       | timestamp | Approval timestamp                                               |
| approval_notes    | text      | Notes from approver                                              |
| fulfilled_by      | bigint    | FK to users                                                      |
| fulfilled_by_name | string    | Name of fulfiller                                                |
| fulfilled_at      | timestamp | Fulfillment timestamp                                            |
| fulfillment_notes | text      | Notes about fulfillment                                          |
| estimated_cost    | decimal   | Estimated cost                                                   |
| actual_cost       | decimal   | Actual cost paid                                                 |
| vendor            | string    | Vendor name                                                      |
| rejection_reason  | text      | Reason for rejection                                             |
| timestamps        |           | created_at, updated_at                                           |

### Workflow

```
1. Staff submits request
   ↓
2. Admin receives notification
   ↓
3. Admin reviews request
   ↓
4a. Approve → Move to procurement → Mark as fulfilled
4b. Reject → Notify staff with reason
```

### Routes

```php
// Resource Management
GET    /admin/hrm/resource-requests              - List all requests
GET    /admin/hrm/resource-requests/create       - Create new request form
POST   /admin/hrm/resource-requests              - Store new request
GET    /admin/hrm/resource-requests/{id}         - View request details
GET    /admin/hrm/resource-requests/{id}/edit    - Edit request form
PUT    /admin/hrm/resource-requests/{id}         - Update request
DELETE /admin/hrm/resource-requests/{id}         - Delete request

// Actions
POST   /admin/hrm/resource-requests/{id}/approve  - Approve request
POST   /admin/hrm/resource-requests/{id}/reject   - Reject request
POST   /admin/hrm/resource-requests/{id}/fulfill  - Mark as fulfilled
```

### Model Methods

**HrmResourceRequest.php**

-   `employee()` - Relationship to employee
-   `approver()` - Relationship to approving user
-   `fulfiller()` - Relationship to fulfilling user
-   `scopePending()` - Get pending requests
-   `scopeApproved()` - Get approved requests
-   `scopeFulfilled()` - Get fulfilled requests
-   `scopeRejected()` - Get rejected requests
-   `isPending()`, `isApproved()`, etc. - Status checks
-   `getStatusColorAttribute()` - Badge color for UI
-   `getPriorityColorAttribute()` - Priority badge color

---

## 💰 Expense Claims System

### Purpose

Enable staff to claim reimbursement for out-of-pocket expenses incurred for work purposes. These claims are automatically integrated into the payroll system.

### Features

#### For Staff

-   Submit expense claims with receipts
-   Multiple expense types (travel, accommodation, meals, transportation, supplies, etc.)
-   Upload receipt documents (PDF, JPG, PNG)
-   Track claim status and payroll inclusion
-   Auto-generated claim numbers (EXP-YYYYMM-XXXX format)

#### For Admins

-   Review and approve/reject expense claims
-   View uploaded receipts
-   Add approval notes
-   Track which claims are included in payroll
-   Filter by status, type, date range
-   Dashboard with financial statistics

#### Payroll Integration

-   **Automatic Inclusion:** Approved claims within a payroll period are automatically included in the employee's payroll calculation
-   **Gross Salary Addition:** Expense claims are added to gross salary (not allowances)
-   **Tracking:** Claims are marked as "included_in_payroll" once processed
-   **Payroll Record Link:** Each claim links to the specific payroll record
-   **Period Tracking:** Start and end dates of payroll period are stored

### Database Schema

**Table:** `hrm_expense_claims`

| Field                 | Type      | Description                                                                  |
| --------------------- | --------- | ---------------------------------------------------------------------------- |
| id                    | bigint    | Primary key                                                                  |
| employee_id           | bigint    | FK to hrm_employees                                                          |
| claim_number          | string    | Auto-generated (EXP-YYYYMM-XXXX)                                             |
| expense_type          | enum      | travel, accommodation, meals, transportation, supplies, communication, other |
| title                 | string    | Claim title                                                                  |
| description           | text      | Detailed description                                                         |
| amount                | decimal   | Claim amount                                                                 |
| currency              | string    | Currency code (default: NPR)                                                 |
| expense_date          | date      | Date expense was incurred                                                    |
| receipt_path          | string    | Path to receipt file                                                         |
| attachments           | json      | Multiple attachments                                                         |
| status                | enum      | pending, approved, rejected, paid                                            |
| approved_by           | bigint    | FK to users                                                                  |
| approved_by_name      | string    | Name of approver                                                             |
| approved_at           | timestamp | Approval timestamp                                                           |
| approval_notes        | text      | Approval notes                                                               |
| rejection_reason      | text      | Rejection reason                                                             |
| payroll_record_id     | bigint    | FK to hrm_payroll_records                                                    |
| included_in_payroll   | boolean   | Is included in payroll                                                       |
| payroll_period_start  | date      | Payroll period start                                                         |
| payroll_period_end    | date      | Payroll period end                                                           |
| paid_at               | timestamp | Payment timestamp                                                            |
| payment_method        | string    | Payment method                                                               |
| transaction_reference | string    | Transaction reference                                                        |
| project_code          | string    | Project code (optional)                                                      |
| cost_center           | string    | Cost center (optional)                                                       |
| notes                 | text      | Additional notes                                                             |
| timestamps            |           | created_at, updated_at                                                       |

### Payroll Integration Schema

**Added to `hrm_payroll_records` table:**

| Field                | Type    | Description                    |
| -------------------- | ------- | ------------------------------ |
| expense_claims       | json    | Array of expense claim details |
| expense_claims_total | decimal | Total amount from all claims   |

### Workflow

```
1. Staff submits expense claim with receipt
   ↓
2. Admin receives notification
   ↓
3. Admin reviews claim and receipt
   ↓
4a. Approve → Claim ready for next payroll
4b. Reject → Notify staff with reason
   ↓
5. During payroll generation:
   - System finds approved claims in payroll period
   - Adds claim amounts to gross salary
   - Marks claims as "included_in_payroll"
   - Links claims to payroll record
   ↓
6. Payroll processed → Claim marked as "paid"
```

### Routes

```php
// Expense Claim Management
GET    /admin/hrm/expense-claims              - List all claims
GET    /admin/hrm/expense-claims/create       - Create new claim form
POST   /admin/hrm/expense-claims              - Store new claim
GET    /admin/hrm/expense-claims/{id}         - View claim details
GET    /admin/hrm/expense-claims/{id}/edit    - Edit claim form
PUT    /admin/hrm/expense-claims/{id}         - Update claim
DELETE /admin/hrm/expense-claims/{id}         - Delete claim

// Actions
POST   /admin/hrm/expense-claims/{id}/approve - Approve claim
POST   /admin/hrm/expense-claims/{id}/reject  - Reject claim
GET    /admin/hrm/expense-claims/ready-for-payroll - Get claims ready for payroll (API)
```

### Model Methods

**HrmExpenseClaim.php**

-   `generateClaimNumber()` - Static method to generate unique claim numbers
-   `employee()` - Relationship to employee
-   `approver()` - Relationship to approving user
-   `payrollRecord()` - Relationship to payroll record
-   `scopePending()`, `scopeApproved()`, `scopePaid()`, `scopeRejected()` - Query scopes
-   `scopeNotInPayroll()` - Claims not yet in payroll
-   `scopeReadyForPayroll()` - Approved claims ready for payroll
-   `isPending()`, `isApproved()`, etc. - Status checks
-   `getStatusColorAttribute()` - Badge color for UI
-   `getFormattedAmountAttribute()` - Formatted amount with currency

### Payroll Calculation Integration

**PayrollCalculationService.php** - Updated Methods

```php
protected function getExpenseClaimsForPeriod($employee, $periodStart, $periodEnd)
```

-   Fetches approved expense claims for the payroll period
-   Returns claims array and total amount
-   Only includes claims not already in payroll

**Updated Payroll Calculation:**

```php
$grossSalary = $basicSalary + $overtimePayment + $allowancesTotal + $expenseClaimsTotal;
```

**Auto-linking on Payroll Creation:**

-   When payroll record is created, expense claims are automatically linked
-   Claims are marked as `included_in_payroll = true`
-   `payroll_record_id` is set
-   Period dates are recorded

---

## 🚀 Usage Examples

### Submitting a Resource Request

```php
// Staff submits a request
$request = HrmResourceRequest::create([
    'employee_id' => auth()->user()->employee->id,
    'item_name' => 'Wireless Keyboard',
    'description' => 'Current keyboard is malfunctioning',
    'quantity' => 1,
    'priority' => 'high',
    'category' => 'technology',
    'reason' => 'Current keyboard keys are not responding properly, affecting productivity',
    'estimated_cost' => 3500.00
]);
```

### Submitting an Expense Claim

```php
// Staff submits an expense claim
$claim = HrmExpenseClaim::create([
    'employee_id' => auth()->user()->employee->id,
    'expense_type' => 'travel',
    'title' => 'Client meeting in Pokhara',
    'description' => 'Transportation and accommodation for client meeting',
    'amount' => 8500.00,
    'currency' => 'NPR',
    'expense_date' => '2025-12-15',
    'receipt_path' => 'expense-receipts/receipt_123.pdf',
]);
// Claim number auto-generated: EXP-202512-0001
```

### Checking Claims in Payroll

```php
// Get approved claims for an employee in a period
$claims = HrmExpenseClaim::where('employee_id', $employeeId)
    ->where('status', 'approved')
    ->where('included_in_payroll', false)
    ->whereBetween('expense_date', [$periodStart, $periodEnd])
    ->get();

$totalExpenses = $claims->sum('amount');
```

---

## 📋 Controller Actions

### HrmResourceRequestController

-   `index()` - List with filters (status, category, priority)
-   `create()` - Show creation form
-   `store()` - Save new request
-   `show()` - View request details
-   `edit()` - Show edit form (pending only)
-   `update()` - Update request (pending only)
-   `approve()` - Approve with notes
-   `reject()` - Reject with reason
-   `fulfill()` - Mark as fulfilled with cost/vendor details
-   `destroy()` - Delete (pending only)

### HrmExpenseClaimController

-   `index()` - List with filters (status, type, date range)
-   `create()` - Show creation form
-   `store()` - Save new claim with receipt upload
-   `show()` - View claim details
-   `edit()` - Show edit form (pending only)
-   `update()` - Update claim (pending only)
-   `approve()` - Approve claim (ready for payroll)
-   `reject()` - Reject with reason
-   `getReadyForPayroll()` - API endpoint for payroll integration
-   `destroy()` - Delete (pending only)

---

## 🔔 Notifications

### Resource Requests

-   **On Submit:** Admins notified of new request
-   **On Approve:** Employee notified of approval
-   **On Reject:** Employee notified with reason
-   **On Fulfill:** Employee notified of fulfillment

### Expense Claims

-   **On Submit:** Admins notified of new claim
-   **On Approve:** Employee notified (claim will be in next payroll)
-   **On Reject:** Employee notified with reason
-   **On Payroll Inclusion:** Automatically tracked

---

## 🎨 UI Components

### Views Created

```
resources/views/admin/hrm/
├── resource-requests/
│   └── index.blade.php    - List all requests with filters
└── expense-claims/
    └── index.blade.php    - List all claims with filters
```

### Color Coding

**Request Statuses:**

-   🟡 Pending - Yellow
-   🔵 Approved - Blue
-   🟢 Fulfilled - Green
-   🔴 Rejected - Red

**Request Priorities:**

-   ⚪ Low - Gray
-   🔵 Medium - Blue
-   🟡 High - Yellow
-   🔴 Urgent - Red

**Expense Claim Statuses:**

-   🟡 Pending - Yellow
-   🟢 Approved - Green
-   🔵 Paid - Blue
-   🔴 Rejected - Red

---

## 🔐 Permissions & Access Control

### Staff (Non-admin)

-   Can create resource requests
-   Can create expense claims
-   Can view own requests/claims only
-   Can edit/delete pending items only

### Admin/Super Admin

-   Can view all requests and claims
-   Can approve/reject requests and claims
-   Can fulfill resource requests
-   Can see financial statistics
-   Can access all management features

---

## 📊 Reporting & Analytics

### Resource Request Statistics

-   Total pending requests
-   Total approved requests
-   Total fulfilled requests
-   Total rejected requests
-   Cost analysis (estimated vs actual)

### Expense Claim Statistics

-   Total pending claims
-   Total approved claims
-   Total paid claims
-   Total pending amount
-   Total approved amount
-   Claims by type breakdown
-   Payroll inclusion tracking

---

## 🔄 Integration Points

### With HRM Employee Module

-   Employee relationships
-   User authentication
-   Department tracking

### With Payroll Module

-   Automatic inclusion of approved expense claims
-   Claims added to gross salary calculation
-   Period-based claim matching
-   Payroll record linkage
-   Claim status updates

### With Notification System

-   Real-time notifications for all actions
-   Admin and employee notifications
-   Status change alerts

---

## 🧪 Testing Checklist

-   [ ] Submit resource request as staff
-   [ ] Approve/reject resource request as admin
-   [ ] Fulfill resource request
-   [ ] Submit expense claim with receipt
-   [ ] Approve expense claim
-   [ ] Generate payroll with approved claims
-   [ ] Verify expense claims in payroll calculation
-   [ ] Verify claims marked as included in payroll
-   [ ] Check notification delivery
-   [ ] Test file upload for receipts
-   [ ] Test filtering and search
-   [ ] Test access control (staff vs admin)

---

## 📝 Future Enhancements

### Resource Requests

-   Budget approval workflow
-   Recurring resource requests
-   Inventory integration
-   Purchase order generation
-   Vendor management

### Expense Claims

-   Multi-currency support with exchange rates
-   Expense policy enforcement
-   Approval workflows (manager → finance → admin)
-   Mileage calculator
-   Per diem rates
-   Tax implications tracking
-   Export to accounting software

---

## 🐛 Known Issues

None at this time.

---

## 📚 Related Documentation

-   [HRM Module Documentation](../MODULES/HRM_MODULE.md)
-   [Payroll System Documentation](../GUIDES/PAYROLL_SYSTEM.md)
-   [Notification System](../MODULES/NOTIFICATION_SYSTEM.md)

---

## 👥 Contributors

-   Implementation Date: December 17, 2025
-   Module: HRM - Resource Requests & Expense Claims
-   Status: ✅ Production Ready

---

## 🎯 Quick Start

### For Staff

1. Navigate to HRM → Resource Requests
2. Click "New Request"
3. Fill in item details and reason
4. Submit and wait for admin approval

5. Navigate to HRM → Expense Claims
6. Click "New Claim"
7. Fill in expense details
8. Upload receipt
9. Submit and wait for approval
10. Approved claims will be included in next payroll automatically

### For Admins

1. Receive notification of new request/claim
2. Review details and attached receipts
3. Approve or reject with notes
4. For resource requests: Mark as fulfilled when completed
5. For expense claims: They will automatically appear in next payroll generation

---

**End of Documentation**
