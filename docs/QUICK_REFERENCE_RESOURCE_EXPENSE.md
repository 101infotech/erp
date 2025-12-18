# Quick Reference: Resource Requests & Expense Claims

## 🎯 Quick Links

-   **Resource Requests Index:** `/admin/hrm/resource-requests`
-   **Expense Claims Index:** `/admin/hrm/expense-claims`
-   **Full Documentation:** [Resource Requests & Expense Claims Module](../MODULES/RESOURCE_REQUESTS_AND_EXPENSE_CLAIMS.md)
-   **Implementation Guide:** [Implementation Details](../IMPLEMENTATION/RESOURCE_REQUESTS_EXPENSE_CLAIMS_IMPLEMENTATION.md)

---

## 📝 Quick Commands

### Run Migrations

```bash
php artisan migrate
```

### Check Routes

```bash
php artisan route:list | grep "resource-requests"
php artisan route:list | grep "expense-claims"
```

### Clear Cache (if needed)

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🚀 Quick Test Checklist

### As Staff

-   [ ] Navigate to Resource Requests
-   [ ] Submit a new request (e.g., "Coffee beans")
-   [ ] Navigate to Expense Claims
-   [ ] Submit a new claim with receipt upload (e.g., "Travel expense")

### As Admin

-   [ ] Check notifications for new submissions
-   [ ] Review resource request details
-   [ ] Approve/Reject a resource request
-   [ ] Mark approved request as fulfilled
-   [ ] Review expense claim with receipt
-   [ ] Approve an expense claim
-   [ ] Generate payroll for the period
-   [ ] Verify expense claim appears in payroll record
-   [ ] Check claim is marked as "included_in_payroll"

---

## 🔧 Common Tasks

### Create a Resource Request (Code Example)

```php
HrmResourceRequest::create([
    'employee_id' => 1,
    'item_name' => 'Wireless Mouse',
    'description' => 'Current mouse is broken',
    'quantity' => 1,
    'priority' => 'medium',
    'category' => 'technology',
    'reason' => 'Essential for work',
    'estimated_cost' => 1200.00
]);
```

### Create an Expense Claim (Code Example)

```php
HrmExpenseClaim::create([
    'employee_id' => 1,
    'expense_type' => 'travel',
    'title' => 'Client Meeting in Pokhara',
    'description' => 'Bus fare and accommodation',
    'amount' => 5000.00,
    'currency' => 'NPR',
    'expense_date' => '2025-12-15',
    'receipt_path' => 'expense-receipts/receipt.pdf',
]);
// Claim number auto-generated: EXP-202512-0001
```

### Query Approved Claims Ready for Payroll

```php
$claims = HrmExpenseClaim::where('employee_id', $employeeId)
    ->where('status', 'approved')
    ->where('included_in_payroll', false)
    ->whereBetween('expense_date', [$periodStart, $periodEnd])
    ->get();
```

---

## 🎨 UI Elements

### Status Badge Colors

**Resource Requests:**

```blade
@if($request->status === 'fulfilled')
    <span class="badge bg-success">Fulfilled</span>
@elseif($request->status === 'approved')
    <span class="badge bg-info">Approved</span>
@elseif($request->status === 'pending')
    <span class="badge bg-warning">Pending</span>
@else
    <span class="badge bg-danger">Rejected</span>
@endif
```

**Expense Claims:**

```blade
@if($claim->included_in_payroll)
    <span class="badge bg-success">In Payroll</span>
@else
    <span class="badge bg-secondary">Pending</span>
@endif
```

---

## 📊 Database Queries

### Get Resource Request Statistics

```sql
SELECT
    status,
    COUNT(*) as count,
    SUM(actual_cost) as total_cost
FROM hrm_resource_requests
GROUP BY status;
```

### Get Expense Claims by Employee

```sql
SELECT
    e.name as employee_name,
    COUNT(ec.id) as total_claims,
    SUM(ec.amount) as total_amount,
    SUM(CASE WHEN ec.included_in_payroll = 1 THEN ec.amount ELSE 0 END) as paid_amount
FROM hrm_employees e
LEFT JOIN hrm_expense_claims ec ON e.id = ec.employee_id
GROUP BY e.id, e.name;
```

### Get Payroll with Expense Claims

```sql
SELECT
    pr.*,
    pr.expense_claims_total,
    COUNT(ec.id) as claims_count
FROM hrm_payroll_records pr
LEFT JOIN hrm_expense_claims ec ON ec.payroll_record_id = pr.id
WHERE pr.employee_id = ?
GROUP BY pr.id;
```

---

## 🐛 Troubleshooting

### Issue: Claims not appearing in payroll

**Solution:** Ensure:

1. Claims are approved
2. Expense dates fall within payroll period
3. Claims are not already included in another payroll
4. Run: `php artisan cache:clear`

### Issue: Receipt upload fails

**Solution:** Check:

1. Storage is properly linked: `php artisan storage:link`
2. `storage/app/public/expense-receipts` directory exists
3. File size is within limits (5MB default)

### Issue: Notifications not working

**Solution:** Verify:

1. NotificationService is properly configured
2. User has valid `user_id` in `hrm_employees` table
3. Check notification logs

---

## 📱 API Endpoints (for future frontend/mobile)

### Resource Requests

```
GET    /api/resource-requests              - List requests
POST   /api/resource-requests              - Create request
GET    /api/resource-requests/{id}         - Get request
PUT    /api/resource-requests/{id}         - Update request
DELETE /api/resource-requests/{id}         - Delete request
POST   /api/resource-requests/{id}/approve - Approve request
POST   /api/resource-requests/{id}/reject  - Reject request
```

### Expense Claims

```
GET    /api/expense-claims                 - List claims
POST   /api/expense-claims                 - Create claim
GET    /api/expense-claims/{id}            - Get claim
PUT    /api/expense-claims/{id}            - Update claim
DELETE /api/expense-claims/{id}            - Delete claim
POST   /api/expense-claims/{id}/approve    - Approve claim
POST   /api/expense-claims/{id}/reject     - Reject claim
```

---

## 🔐 Security Notes

-   Only admins can approve/reject requests and claims
-   Staff can only view/edit their own submissions
-   File uploads are validated (type, size)
-   All actions are logged with user information
-   Receipts stored securely in private storage

---

## 💡 Tips

1. **For Resource Requests:**

    - Encourage staff to provide detailed reasons
    - Set realistic estimated costs
    - Track vendor preferences for frequently requested items

2. **For Expense Claims:**

    - Always upload clear receipt images
    - Submit claims promptly (within same month if possible)
    - Use project codes for better tracking
    - Approved claims will automatically be in next payroll

3. **For Admins:**
    - Review claims within payroll period for timely processing
    - Add notes when approving to maintain audit trail
    - Monitor spending patterns through statistics
    - Mark requests as fulfilled promptly

---

## 📞 Need Help?

-   Check full documentation in `/docs/MODULES/`
-   Review implementation guide in `/docs/IMPLEMENTATION/`
-   Check model methods in `/app/Models/`
-   Review controller actions in `/app/Http/Controllers/Admin/`

---

**Last Updated:** December 17, 2025
