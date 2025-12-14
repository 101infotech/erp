# Payroll Collision Detection - Quick Reference

## What It Does

✅ **Prevents duplicate payrolls** - Can't create overlapping payroll periods for the same employee  
✅ **Shows clear warnings** - Tells you exactly which employees and dates conflict  
✅ **Easy to fix** - Delete the old draft or change your dates  
✅ **Safe deletion** - Only draft payrolls can be deleted

---

## When You'll See It

### Generating Payroll

When you try to generate payroll and an employee already has a payroll record for those dates, you'll see a **red warning box** showing:

-   Which employees have conflicts
-   The exact dates of existing payrolls
-   Whether they're draft, approved, or paid
-   Links to view or delete the conflicting records

---

## How to Fix Collisions

### Option 1: Delete the Existing Draft

1. In the collision warning, click **"View/Delete"** button
2. Review the existing payroll
3. Click the red **"Delete"** button
4. Confirm deletion
5. Go back and generate payroll again

### Option 2: Change Your Dates

1. Look at the existing payroll dates in the warning
2. Change your start/end dates to avoid overlap
3. Try generating again

### Option 3: Deselect Conflicting Employees

1. Uncheck employees that have collisions
2. Generate payroll for the others
3. Handle conflicting employees separately

---

## Deleting Payroll Records

### Where You Can Delete

**From Index Page** (Payroll List):

-   Click **Delete** link next to draft payrolls

**From Detail Page** (Payroll Show):

-   Click red **Delete** button at top right

**From Collision Warning**:

-   Click **View/Delete** button, then delete from detail page

### Important Rules

❌ **Cannot delete approved payrolls** - Only drafts  
❌ **Cannot delete paid payrolls** - Only drafts  
✅ **Can delete draft payrolls** - Anytime before approval

---

## Examples

### ✅ No Collision - Sequential Periods

```
Existing: Dec 1 - Dec 15
New:      Dec 16 - Dec 31
Result:   Payroll generated successfully
```

### ❌ Collision - Overlap

```
Existing: Dec 1 - Dec 15
New:      Dec 10 - Dec 20
Result:   Collision warning shown
Fix:      Change dates or delete existing draft
```

### ❌ Collision - Same Dates

```
Existing: Dec 1 - Dec 15
New:      Dec 1 - Dec 15
Result:   Collision warning shown
Fix:      Delete existing draft or choose different dates
```

---

## Quick Tips

💡 **Plan Your Periods**: Make sure payroll periods don't overlap before generating  
💡 **Review Before Approving**: Once approved, you can't delete it  
💡 **Use Sequential Dates**: Dec 1-15, Dec 16-31 works perfectly  
💡 **Check Existing Records**: Review the payroll list before creating new ones  
💡 **Delete Unwanted Drafts**: Clean up draft payrolls you don't need

---

## Common Questions

**Q: Can I have multiple payrolls for the same employee?**  
A: Yes! As long as the date periods don't overlap.

**Q: What if I need to delete an approved payroll?**  
A: You can't. Only draft payrolls can be deleted. Contact system admin if needed.

**Q: Will it check for collisions when generating for multiple employees?**  
A: Yes! It checks each employee individually and shows all conflicts.

**Q: What happens to the PDF when I delete a payroll?**  
A: The PDF file is automatically deleted if it exists.

**Q: Can I override a collision?**  
A: No. You must either delete the existing record or choose different dates.

---

## Workflow Example

**Scenario**: Generate December payroll, but realize there's already a draft

1. ✅ Navigate to Generate Payroll
2. ✅ Select employees: Sagar, Ram, Shyam
3. ✅ Set dates: Dec 1 - Dec 31
4. ✅ Click "Generate Payroll"
5. ❌ **Collision Warning!** Shows Sagar has existing payroll for Dec 1-15
6. ✅ Click "View/Delete" on Sagar's existing payroll
7. ✅ Review the draft - it's incomplete
8. ✅ Click "Delete" button
9. ✅ Confirm deletion
10. ✅ Return to Generate Payroll
11. ✅ Click "Generate Payroll" again
12. ✅ **Success!** All three payrolls created

---

## Status Indicators

| Status   | Badge Color | Can Delete? | In Collision Warning |
| -------- | ----------- | ----------- | -------------------- |
| Draft    | 🟡 Yellow   | ✅ Yes      | "View/Delete" button |
| Approved | 🔵 Blue     | ❌ No       | "View Details" link  |
| Paid     | 🟢 Green    | ❌ No       | "View Details" link  |

---

## Need Help?

1. Check the collision warning for details
2. Review existing payroll records in the list
3. Delete unwanted drafts before generating new ones
4. Use sequential date periods to avoid overlaps
5. Contact admin if you need to modify approved payrolls

---

**Remember**: The system is protecting you from creating duplicate or conflicting payroll records. Work with it, not against it!
