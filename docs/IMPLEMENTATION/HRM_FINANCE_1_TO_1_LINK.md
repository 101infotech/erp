# HRM ↔ Finance Company 1:1 Link

Date: 2025-12-17

## Summary
- Establishes a strict one-to-one relationship between HRM Companies (`hrm_companies`) and Finance Companies (`finance_companies`).
- Adds a foreign key `hrm_companies.finance_company_id` with a unique index.
- HRM UI now requires selecting a Finance Company when creating/editing an HRM Company.

## Schema Changes
- Migration: `2025_12_17_130000_add_finance_company_id_to_hrm_companies_table.php`
  - Adds `finance_company_id` (nullable during rollout) with `UNIQUE` and FK to `finance_companies.id` (`ON DELETE SET NULL`).

## Eloquent Relations
- `App\Models\HrmCompany`
  - `financeCompany(): BelongsTo`
- `App\Models\FinanceCompany`
  - `hrmCompany(): HasOne`

## Controller Updates
- `App\Http\Controllers\Admin\HrmCompanyController`
  - `create/edit`: loads `$financeCompanies` for selection.
  - `store/update` validation includes `finance_company_id` with unique rule to enforce 1:1.

## Views Updated
- `resources/views/admin/hrm/companies/create.blade.php`
- `resources/views/admin/hrm/companies/edit.blade.php`
- `resources/views/admin/hrm/companies/show.blade.php` (displays link to Finance Company)

## Rollout Steps
1. Run migrations:
```bash
php artisan migrate
```
2. Link existing HRM companies by editing them and selecting the corresponding Finance Company.
3. (Optional) Once all links are set, consider making `finance_company_id` non-nullable in a follow-up migration.

## Notes
- Departments already reference `finance_company_id` for Finance context; this change ensures HRM Company aligns consistently.
- Validation prevents two HRM companies from linking to the same Finance Company.
