# Finance Module - Quick Reference Card

## 🚀 Quick Commands

### Sync Payroll to Finance
```bash
# View what would be synced (dry run)
php artisan finance:sync-payroll --status=all --dry-run

# Sync all approved and paid payrolls
php artisan finance:sync-payroll --status=all

# Sync specific date range
php artisan finance:sync-payroll --from=2025-01-01 --to=2025-12-31

# Sync only approved payrolls
php artisan finance:sync-payroll --status=approved

# Sync only paid payrolls
php artisan finance:sync-payroll --status=paid
```

## 📊 Common Tasks

### View Finance Transactions
```
URL: /admin/finance/transactions
Filter: Reference contains "PAYROLL" to see payroll expenses
```

### Create Expense
```
1. Go to: Admin > Finance > Transactions > Create
2. Select company
3. Choose category
4. Enter amount
5. Add description
6. Select payment method
7. Save
```

### Export Founders
```
1. Go to: Admin > Finance > Founders
2. Click "Export" button
3. CSV file downloads automatically
```

## 🔗 Integration Flow

### When Payroll is Approved
```
Payroll Approved
    ↓
Finance Transaction Created
    ↓
Type: expense
Amount: net_salary
Reference: PAYROLL-{id}
Status: approved
Category: Payroll Expenses
```

### When Payroll is Paid
```
Payroll Marked as Paid
    ↓
Finance Transaction Updated
    ↓
Status: completed
Payment Method: Updated
```

## 📁 Important Files

### Service
```
app/Services/PayrollFinanceIntegrationService.php
```

### Controllers
```
app/Http/Controllers/Admin/HrmPayrollController.php
app/Http/Controllers/Admin/FinanceFounderController.php
app/Http/Controllers/Admin/Finance*.php (16 controllers)
```

### Commands
```
app/Console/Commands/SyncPayrollToFinance.php
```

### Models
```
app/Models/Finance*.php (22 models)
```

## 🗂️ Finance Module Sections

| Section | URL | Purpose |
|---------|-----|---------|
| Dashboard | `/admin/finance/dashboard` | Overview & KPIs |
| Companies | `/admin/finance/companies` | Manage companies |
| Transactions | `/admin/finance/transactions` | All transactions |
| Sales | `/admin/finance/sales` | Sales & invoices |
| Purchases | `/admin/finance/purchases` | Purchases & bills |
| Customers | `/admin/finance/customers` | Customer records |
| Vendors | `/admin/finance/vendors` | Vendor records |
| Budgets | `/admin/finance/budgets` | Budget planning |
| Assets | `/admin/finance/assets` | Asset management |
| Founders | `/admin/finance/founders` | Founder tracking |
| Reports | `/admin/finance/reports` | Financial reports |

## 🔍 Finding Payroll Expenses

### Method 1: Filter by Reference
```
1. Go to Finance > Transactions
2. Filter: Reference Number
3. Search: "PAYROLL"
```

### Method 2: Filter by Category
```
1. Go to Finance > Transactions
2. Filter: Category
3. Select: "Payroll Expenses"
```

### Method 3: Direct Query
```sql
SELECT * FROM finance_transactions 
WHERE reference_number LIKE 'PAYROLL-%'
ORDER BY created_at DESC;
```

## 💡 Transaction Number Format

```
TRX-PAY-YYYYMMDD-NNNN

Examples:
TRX-PAY-20260105-0001
TRX-PAY-20260105-0002
TRX-PAY-20260106-0001
```

## 🎯 Status Flow

### Payroll Status
```
draft → approved → paid
```

### Finance Transaction Status
```
draft → pending → approved → completed → cancelled
```

### Mapping
```
Payroll "approved" → Transaction "approved"
Payroll "paid" → Transaction "completed"
```

## ⚙️ Configuration

### Link HR Company to Finance Company
```sql
UPDATE hrm_companies 
SET finance_company_id = 1 
WHERE id = 1;
```

### Create Payroll Category (Auto-created)
```
Name: "Payroll Expenses"
Type: expense
Auto-created when first payroll approved
```

## 📝 Transaction Data Structure

```json
{
  "company_id": 1,
  "transaction_number": "TRX-PAY-20260105-0001",
  "transaction_date_bs": "2082-09-22",
  "transaction_type": "expense",
  "category_id": 5,
  "amount": 75000.00,
  "description": "Payroll expense for John Doe...",
  "payment_method": "bank_transfer",
  "reference_number": "PAYROLL-123",
  "notes": {
    "payroll_id": 123,
    "employee_id": 45,
    "employee_name": "John Doe",
    "net_salary": 75000.00,
    ...
  },
  "status": "approved"
}
```

## 🛠️ Troubleshooting

### Issue: No transactions created
**Check:**
1. Logs: `storage/logs/laravel.log`
2. Finance company exists and is active
3. HR company has finance_company_id set

### Issue: Duplicate transactions
**Solution:**
- System prevents duplicates via reference_number
- Safe to run sync command multiple times

### Issue: Export not working
**Fix:**
```bash
mkdir -p storage/app/exports
chmod -R 775 storage/app/exports
```

## 📚 Documentation

- Full Integration Guide: `docs/IMPLEMENTATION/FINANCE_PAYROLL_INTEGRATION.md`
- Complete Summary: `docs/IMPLEMENTATION/FINANCE_MODULE_COMPLETE_SUMMARY.md`
- Database Fix: `docs/FIXES/FINANCE_MODULE_FIX_2026_01_05.md`

## ✅ Verification Checklist

- [ ] Sync historical payrolls
- [ ] Link HR companies to finance companies
- [ ] Approve a test payroll
- [ ] Verify finance transaction created
- [ ] Mark payroll as paid
- [ ] Verify transaction updated
- [ ] Export founders list
- [ ] Review transaction in finance module

## 🔐 Permissions

All finance routes require:
- ✅ Authentication
- ✅ Admin role
- ✅ Active user status

## 📊 Reporting

### Available Reports
1. Transaction List (with filters)
2. Budget vs Actual
3. Founder Balance Summary
4. Asset Register
5. Sales & Purchase Reports

### Coming Soon
- Profit & Loss Statement
- Balance Sheet
- Cash Flow Statement
- Payroll Expense Analysis

## 🚨 Important Notes

1. **Non-Blocking**: Finance integration never blocks payroll operations
2. **Auto-Recovery**: Missing transactions can be created via sync command
3. **Audit Trail**: All transactions logged with full details
4. **Safety**: Dry-run mode available for testing

## 🎓 Training Tips

### For Finance Staff
1. Start with dashboard for overview
2. Use filters to narrow down transactions
3. Export reports regularly
4. Review payroll expenses monthly

### For Admins
1. Set up company links first
2. Run historical sync once
3. Monitor logs for errors
4. Regular backups recommended

---

**Status:** ✅ Production Ready  
**Version:** 1.0  
**Last Updated:** January 5, 2026

*Keep this card handy for quick reference!*
