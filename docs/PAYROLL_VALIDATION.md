# Payroll Validation vs Excel Sheet

This document validates our ERP payroll calculations against the Excel-based approach shown in the screenshots and highlights feature mappings and gaps.

## Excel Columns and Formulas

-   **Total Days in a Month**: BS month days (e.g., 30).
-   **Total Payable Days**: Present days + Paid leaves; capped at working days in period.
-   **Per Day Amount**: `monthly_basic_salary / month_total_days`.
-   **Total Amount**: `Per Day Amount * Total Payable Days`.
-   **TDS**: Tax withheld.
-   **Advance Taken**: Prior advances deducted.
-   **Other Expenses**: Additional deductions.
-   **In Hand Salary**: `Total Amount - TDS - Advance - Other Expenses`.

## System Calculations (Current)

-   **Month total days**: Auto from BS via `NepalCalendarService::getDaysInMonth` or admin override.
-   **Days to pay** (monthly): `days_worked + paid_leave_days`, capped to work days in period (excludes Saturdays).
-   **Per day rate**: `basic_salary_npr / month_total_days`.
-   **Basic salary (period)**: `per_day_rate * days_to_pay`.
-   **Allowances**: Sum of employee allowances; included in gross.
-   **Overtime payment**: Manual input (overtime hours tracked separately).
-   **Gross salary**: `basic_salary + overtime_payment + allowances_total`.
-   **Tax (TDS)**: `NepalTaxCalculationService::calculateTaxForPeriod(gross_salary, monthsInPeriod)`; override supported.
-   **Unpaid leave deduction**: For monthly salary: `per_day_rate * unpaid_leave_days`.
-   **Other deductions**: Sum of employee deductions (maps to "Other Expenses").
-   **Hourly deduction (suggested/approved)**: Based on missing working hours; applied after approval.
-   **Advance payment**: Field supported and deducted from net.
-   **Net salary (In Hand)**: `gross_salary - tax_amount - deductions_total` then minus approved hourly deduction and advance (applied via admin edit).

## Field Mapping to Excel

-   Excel "Per Day Amount" → System `per_day_rate` (derived inside calculation).
-   Excel "Total Payable Days" → System `days_to_pay` (days_worked + paid_leave_days, capped).
-   Excel "Total Amount" → System `basic_salary` for period.
-   Excel "TDS" → System `tax_amount`.
-   Excel "Advance Taken" → System `advance_payment`.
-   Excel "Other Expenses" → System `deductions_total` (includes unpaid leave deduction + custom deductions; hourly deduction if approved).
-   Excel "In Hand Salary" → System `net_salary` after admin adjustments (tax override, hourly deduction approval, advance set).

## Additional Features (System)

-   **Paid leave handling**: Excludes Saturdays from paid leave count; paid leaves add to payable days/hours.
-   **Attendance integration**: Jibble sync (attendance, payroll hours, overtime) → `HrmAttendanceDay`.
-   **Anomaly detection**: Flags suspicious patterns; stored and shown in payroll review.
-   **Tax override with reason**: Adjusts `tax_amount` and re-computes `net_salary`.
-   **Payslip PDF**: Generated with breakdown (basic, OT, allowances, tax, deductions, net).

## Observed Alignment

-   Daily rate and payable days match Excel logic.
-   TDS and in-hand salary computations align after considering allowances and overtime (Excel "Total Amount" typically excludes these; our gross includes them before tax).
-   Unpaid leave deduction is explicit in system (often implicit in Excel via reduced payable days).

## Minor Differences / Notes

-   Excel’s "Other Expenses" is a single column; system supports multiple deductions plus optional hourly deduction, all summed in `deductions_total`.
-   Excel screenshots show fixed per day amounts; our rate is derived per BS month and works for partial periods.
-   If you want net salary to mirror Excel strictly without allowances/overtime affecting TDS, set allowances/overtime to 0 or adjust tax policy; currently TDS is applied on gross.

## Recommendations

-   Confirm whether TDS should apply on gross (basic + OT + allowances) or just basic; update `NepalTaxCalculationService` accordingly if needed.
-   Ensure UI labels clarify: show "Per Day Amount" and "Total Payable Days" in payroll detail to mirror Excel.
-   When posting payroll, have admins confirm hourly deduction and advance to finalize net salary matching Excel’s "In Hand".

No code change is required for core parity; only UI surfacing of per-day and payable-day values may be added for clarity.
