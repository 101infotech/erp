# Payroll Date Collision Detection & Deletion

## Overview

This feature prevents duplicate or overlapping payroll records for employees by detecting date collisions during payroll generation. It also provides the ability to delete draft payroll records.

**Implementation Date**: December 10, 2025  
**Status**: ✅ Complete

---

## Features

### 1. Date Collision Detection

-   **Automatic checking** during payroll generation
-   **Prevents overlapping** payroll periods for the same employee
-   **Detailed collision reporting** showing exactly which employees and dates conflict
-   **Smart overlap detection** catches all scenarios:
    -   New period starts during existing period
    -   New period ends during existing period
    -   New period completely encompasses existing period
    -   Existing period completely within new period

### 2. Collision Reporting

-   **Visual warnings** on the create page showing collision details
-   **Per-employee breakdown** of conflicting payroll records
-   **Date display** in both BS (Bikram Sambat) and AD (Gregorian) formats
-   **Status indicators** showing if conflicting payroll is draft, approved, or paid
-   **Quick actions** to view or delete conflicting draft payrolls

### 3. Payroll Deletion

-   **Delete draft payrolls** to resolve collisions
-   **Safety restrictions** - only draft payrolls can be deleted
-   **Automatic cleanup** - removes PDF files if they exist
-   **Delete from multiple places**:
    -   Payroll detail page (show view)
    -   Payroll list page (index view)
    -   Collision warning interface

---

## Technical Implementation

### Database Schema

No new fields required. Uses existing fields:

-   `employee_id` - Links to employee
-   `period_start_ad` - Period start in AD format
-   `period_end_ad` - Period end in AD format
-   `status` - Draft, approved, or paid

### Model: `HrmPayrollRecord`

#### New Static Method: `checkDateCollisions()`

```php
public static function checkDateCollisions(
    array $employeeIds,
    string $periodStartAd,
    string $periodEndAd,
    ?int $excludePayrollId = null
): array
```

**Parameters**:

-   `$employeeIds` - Array of employee IDs to check
-   `$periodStartAd` - Period start date in AD format (Y-m-d)
-   `$periodEndAd` - Period end date in AD format (Y-m-d)
-   `$excludePayrollId` - Optional payroll ID to exclude (for future update functionality)

**Returns**:

```php
[
    'has_collision' => bool,
    'collisions' => [
        [
            'employee_id' => int,
            'employee_name' => string,
            'employee_code' => string,
            'existing_payrolls' => [
                [
                    'id' => int,
                    'period_start_bs' => string,
                    'period_end_bs' => string,
                    'period_start_ad' => string,
                    'period_end_ad' => string,
                    'status' => string
                ],
                // ... more payrolls
            ]
        ],
        // ... more employees with collisions
    ]
]
```

**Logic**:
The method checks for date overlaps using SQL queries that detect:

1. **New period starts during existing period**:

    ```sql
    period_start_ad BETWEEN existing_start AND existing_end
    ```

2. **New period ends during existing period**:

    ```sql
    period_end_ad BETWEEN existing_start AND existing_end
    ```

3. **New period encompasses existing period**:
    ```sql
    existing_start >= period_start_ad AND existing_end <= period_end_ad
    ```

### Controller: `HrmPayrollController`

#### Updated `store()` Method

**Before Generation**:

```php
// Check for date collisions
$collisionCheck = HrmPayrollRecord::checkDateCollisions(
    $validated['employee_ids'],
    $periodStart->format('Y-m-d'),
    $periodEnd->format('Y-m-d')
);

if ($collisionCheck['has_collision']) {
    return back()
        ->withInput()
        ->with('collision_error', true)
        ->with('collisions', $collisionCheck['collisions'])
        ->withErrors(['period_start' => 'Date collision detected!']);
}
```

**Flow**:

1. Validate input
2. Convert dates to proper formats
3. **Check for collisions** (NEW)
4. If collisions found, return with detailed error
5. If no collisions, proceed with generation

#### New `destroy()` Method

```php
public function destroy($id)
{
    $payroll = HrmPayrollRecord::findOrFail($id);

    // Only allow deletion of draft payrolls
    if ($payroll->status !== 'draft') {
        return back()->with('error', 'Only draft payrolls can be deleted.');
    }

    DB::transaction(function () use ($payroll) {
        // Delete PDF file if exists
        if ($payroll->payslip_pdf_path && file_exists($payroll->payslip_pdf_path)) {
            try {
                unlink($payroll->payslip_pdf_path);
            } catch (\Exception $e) {
                Log::warning('Failed to delete PDF file');
            }
        }

        // Delete the payroll record
        $payroll->delete();
    });

    return redirect()->route('admin.hrm.payroll.index')
        ->with('success', 'Payroll record deleted successfully.');
}
```

**Safety Features**:

-   ✅ Only draft payrolls can be deleted
-   ✅ PDF files are cleaned up
-   ✅ Database transaction for consistency
-   ✅ Error handling for file deletion
-   ✅ Confirmation dialog before deletion

### Routes

**New Route**:

```php
Route::delete('payroll/{payroll}', [HrmPayrollController::class, 'destroy'])
    ->name('payroll.destroy');
```

**Full Route List**:

```php
Route::get('payroll', [HrmPayrollController::class, 'index'])->name('payroll.index');
Route::get('payroll/create', [HrmPayrollController::class, 'create'])->name('payroll.create');
Route::post('payroll', [HrmPayrollController::class, 'store'])->name('payroll.store');
Route::get('payroll/{payroll}', [HrmPayrollController::class, 'show'])->name('payroll.show');
Route::get('payroll/{payroll}/edit', [HrmPayrollController::class, 'edit'])->name('payroll.edit');
Route::put('payroll/{payroll}', [HrmPayrollController::class, 'update'])->name('payroll.update');
Route::delete('payroll/{payroll}', [HrmPayrollController::class, 'destroy'])->name('payroll.destroy'); // NEW
Route::post('payroll/{payroll}/approve', [HrmPayrollController::class, 'approve'])->name('payroll.approve');
// ... other routes
```

---

## User Interface

### 1. Create Payroll Page - Collision Warning

When a collision is detected, a large red warning box appears showing:

**Warning Box Features**:

-   ❌ Red alert icon and border
-   📋 List of affected employees
-   📅 Details of each conflicting payroll record
-   🏷️ Status badges (draft, approved, paid)
-   🔗 Quick links to view or delete conflicting records
-   💡 Solution suggestions

**Visual Structure**:

```
┌─────────────────────────────────────────────────────┐
│ ⚠️ Date Collision Detected!                         │
│                                                      │
│ The following employees already have payroll        │
│ records that overlap with the selected period:      │
│                                                      │
│ ┌─────────────────────────────────────────────────┐ │
│ │ Sagar Chhetri (SAG001)                          │ │
│ │                                                  │ │
│ │ Existing Payroll Records:                       │ │
│ │ ┌─────────────────────────────────────────────┐ │ │
│ │ │ Period (BS): 2081/08/16 to 2081/08/30      │ │ │
│ │ │ Period (AD): 2024-12-01 to 2024-12-15      │ │ │
│ │ │ Status: [Draft]               [View/Delete] │ │ │
│ │ └─────────────────────────────────────────────┘ │ │
│ └─────────────────────────────────────────────────┘ │
│                                                      │
│ Solution: Delete the existing draft payroll or      │
│ select a different date range that doesn't overlap. │
└─────────────────────────────────────────────────────┘
```

### 2. Payroll Index Page - Delete Button

**For Draft Payrolls**:

```
Actions: View | Edit | Delete
```

**For Approved/Paid Payrolls**:

```
Actions: View
```

### 3. Payroll Detail Page - Delete Button

**Location**: Top right button group, next to Edit and Approve buttons

**Appearance**: Red button with trash icon

**Behavior**:

-   Only visible for draft payrolls
-   Shows confirmation dialog before deletion
-   Redirects to index page after successful deletion

---

## User Workflows

### Workflow 1: Collision Detected - Delete Existing

1. **Admin navigates** to Generate Payroll
2. **Admin selects** employees and period
3. **Admin clicks** "Generate Payroll"
4. **System detects** collision with existing draft
5. **System shows** collision warning with details
6. **Admin clicks** "View/Delete" on conflicting record
7. **Admin reviews** the existing payroll
8. **Admin clicks** Delete button
9. **Admin confirms** deletion in dialog
10. **System deletes** the draft payroll
11. **Admin returns** to generate page
12. **Admin clicks** "Generate Payroll" again
13. **System generates** new payroll successfully

### Workflow 2: Collision Detected - Change Dates

1. **Admin navigates** to Generate Payroll
2. **Admin selects** employees and period
3. **Admin clicks** "Generate Payroll"
4. **System detects** collision
5. **System shows** collision warning with details
6. **Admin reviews** the dates in warning
7. **Admin changes** period dates to avoid overlap
8. **Admin clicks** "Generate Payroll"
9. **System generates** payroll successfully

### Workflow 3: Delete Draft from List

1. **Admin navigates** to Payroll Management
2. **Admin sees** list of all payrolls
3. **Admin identifies** draft payroll to delete
4. **Admin clicks** Delete link
5. **Admin confirms** deletion
6. **System deletes** payroll
7. **Admin sees** success message

---

## Collision Detection Examples

### Example 1: Exact Overlap

```
Existing: Dec 1 - Dec 15
New:      Dec 1 - Dec 15
Result:   ❌ COLLISION (exact match)
```

### Example 2: New Period Starts During Existing

```
Existing: Dec 1 - Dec 15
New:      Dec 10 - Dec 20
Result:   ❌ COLLISION (Dec 10 is within existing period)
```

### Example 3: New Period Ends During Existing

```
Existing: Dec 10 - Dec 20
New:      Dec 5 - Dec 15
Result:   ❌ COLLISION (Dec 15 is within existing period)
```

### Example 4: New Period Encompasses Existing

```
Existing: Dec 10 - Dec 15
New:      Dec 1 - Dec 31
Result:   ❌ COLLISION (existing period completely within new)
```

### Example 5: Existing Period Encompasses New

```
Existing: Dec 1 - Dec 31
New:      Dec 10 - Dec 15
Result:   ❌ COLLISION (new period completely within existing)
```

### Example 6: No Overlap - Before

```
Existing: Dec 15 - Dec 31
New:      Dec 1 - Dec 14
Result:   ✅ NO COLLISION (new period ends before existing starts)
```

### Example 7: No Overlap - After

```
Existing: Dec 1 - Dec 15
New:      Dec 16 - Dec 31
Result:   ✅ NO COLLISION (new period starts after existing ends)
```

### Example 8: Same Day Boundary

```
Existing: Dec 1 - Dec 15
New:      Dec 15 - Dec 31
Result:   ❌ COLLISION (Dec 15 is shared)
```

---

## Edge Cases Handled

### 1. Multiple Existing Payrolls

**Scenario**: Employee has multiple existing payrolls

**Handling**:

-   All conflicting payrolls are shown in collision warning
-   Admin can see and manage each one separately
-   System prevents generation until all collisions resolved

### 2. Multiple Employees

**Scenario**: Bulk generation with some employees having collisions

**Handling**:

-   Collision check is per-employee
-   Warning shows all employees with collisions
-   Can select/deselect employees to avoid collisions
-   Or resolve collisions individually

### 3. Approved/Paid Payrolls

**Scenario**: Collision with approved or paid payroll

**Handling**:

-   Collision is still detected and prevented
-   Admin cannot delete approved/paid payrolls
-   Warning shows status clearly
-   Admin must choose different dates

### 4. Same Employee, Different Periods

**Scenario**: Employee has payroll for Jan, now generating for Feb

**Handling**:

-   ✅ No collision if periods don't overlap
-   System allows multiple non-overlapping periods
-   Employees can have many payroll records for different periods

### 5. PDF File Deletion

**Scenario**: Deleting payroll with existing PDF

**Handling**:

-   System attempts to delete PDF file
-   Logs warning if file deletion fails
-   Continues with database deletion
-   No data corruption if file deletion fails

---

## Benefits

### For Administrators

1. **Prevents Errors**: Can't accidentally create duplicate payrolls
2. **Clear Feedback**: Knows exactly which employees and dates conflict
3. **Easy Resolution**: Can delete drafts or adjust dates quickly
4. **Audit Trail**: Can see all existing payrolls before deciding
5. **Confidence**: System validates data integrity automatically

### For the System

1. **Data Integrity**: No overlapping payroll periods per employee
2. **Clean Data**: Easy to query and report on payroll
3. **Predictable**: Each period has exactly one payroll per employee
4. **Maintainable**: Easy to identify and fix issues
5. **Scalable**: Works efficiently even with many payrolls

### For Employees

1. **Accurate Records**: No duplicate or conflicting payslips
2. **Trust**: System ensures payroll is processed correctly
3. **Clarity**: Each period has clear, single payroll record

---

## Testing Scenarios

### Test 1: Basic Collision Detection

1. Create draft payroll for Employee A: Dec 1-15
2. Try to create payroll for Employee A: Dec 1-15
3. ✅ Expect: Collision warning shown

### Test 2: Partial Overlap

1. Create draft payroll for Employee A: Dec 1-15
2. Try to create payroll for Employee A: Dec 10-20
3. ✅ Expect: Collision warning shown

### Test 3: No Collision - Sequential

1. Create draft payroll for Employee A: Dec 1-15
2. Try to create payroll for Employee A: Dec 16-31
3. ✅ Expect: Payroll generated successfully

### Test 4: Delete Draft

1. Create draft payroll
2. Navigate to payroll detail page
3. Click Delete button
4. Confirm deletion
5. ✅ Expect: Payroll deleted, redirected to index

### Test 5: Cannot Delete Approved

1. Create and approve payroll
2. Navigate to payroll detail page
3. ✅ Expect: Delete button not visible

### Test 6: Bulk Generation with Collisions

1. Create draft payroll for Employee A: Dec 1-15
2. Try to generate for Employee A, B, C: Dec 1-15
3. ✅ Expect: Collision warning showing only Employee A

### Test 7: Resolve via Deletion

1. Create draft payroll for Employee A: Dec 1-15
2. Try to create payroll for Employee A: Dec 1-15
3. See collision warning
4. Click "View/Delete" link
5. Delete the existing draft
6. Return to generate page
7. Try again
8. ✅ Expect: Payroll generated successfully

---

## Files Modified

### Backend

1. **app/Models/HrmPayrollRecord.php**

    - Added `checkDateCollisions()` static method
    - Comprehensive overlap detection logic

2. **app/Http/Controllers/Admin/HrmPayrollController.php**

    - Updated `store()` method with collision check
    - Added `destroy()` method for deletion

3. **routes/web.php**
    - Added `DELETE` route for payroll deletion

### Frontend

4. **resources/views/admin/hrm/payroll/create.blade.php**

    - Added collision warning display section
    - Shows detailed collision information
    - Provides resolution suggestions

5. **resources/views/admin/hrm/payroll/show.blade.php**

    - Added Delete button for draft payrolls
    - Confirmation dialog before deletion

6. **resources/views/admin/hrm/payroll/index.blade.php**
    - Added Delete link in actions column for drafts

### Documentation

7. **docs/PAYROLL_COLLISION_DETECTION.md** (this file)
    - Complete feature documentation

---

## Configuration

### No Configuration Required

The collision detection works automatically with no configuration needed.

### Constants Used

**Status Values**:

-   `draft` - Payroll can be deleted
-   `approved` - Payroll cannot be deleted
-   `paid` - Payroll cannot be deleted

**Date Format**:

-   AD dates: `Y-m-d` (2024-12-01)
-   BS dates: String format from nepali_date helper

---

## Performance Considerations

### Database Queries

-   One query per employee during collision check
-   Uses indexed `employee_id` and date columns
-   Efficient SQL with `whereRaw` for date comparisons

### Optimization

-   Only loads necessary fields for collision check
-   Uses eager loading for employee relationship
-   Minimal data transferred in collision response

### Scalability

-   Works efficiently with:
    -   Hundreds of employees
    -   Thousands of payroll records
    -   Bulk generation of payrolls

---

## Future Enhancements

### Potential Improvements

1. **Soft Deletes**

    - Keep deleted payrolls in archive
    - Allow recovery if needed
    - Audit trail of deletions

2. **Collision Override**

    - Allow admin to override collision with reason
    - Log override decision
    - Require special permission

3. **Auto-Resolution**

    - Suggest optimal date ranges
    - Show calendar view of existing payrolls
    - Visual timeline of payroll periods

4. **Batch Deletion**

    - Delete multiple draft payrolls at once
    - Useful for bulk cleanup
    - With confirmation dialog

5. **Collision Reports**
    - Generate reports of all collisions
    - Export for analysis
    - Trend analysis

---

## Troubleshooting

### Issue: Collision detected but dates don't overlap visually

**Cause**: Remember that BETWEEN includes both boundary dates

**Solution**: Check if dates share the same day (e.g., existing ends Dec 15, new starts Dec 15)

### Issue: Cannot delete payroll

**Cause**: Payroll status is not 'draft'

**Solution**: Only draft payrolls can be deleted. Approved/paid payrolls are protected.

### Issue: PDF file not deleted

**Cause**: File permissions or file doesn't exist

**Solution**: Check storage permissions. Error is logged but doesn't block deletion.

### Issue: Collision warning doesn't show

**Cause**: Session data not persisted

**Solution**: Ensure session middleware is active. Check for `->withInput()` in controller.

---

## Summary

✅ **Complete Implementation** of collision detection and deletion  
✅ **Zero Data Integrity Issues** - prevents overlaps  
✅ **User-Friendly Interface** - clear warnings and solutions  
✅ **Safe Deletion** - only drafts, with confirmations  
✅ **Production Ready** - tested and documented

This feature ensures that the payroll system maintains data integrity while providing administrators with the tools they need to manage payroll records effectively and safely.
