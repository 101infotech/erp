# Phase 2: Asset Management - Quick Start Guide

**Target Users:** Finance Administrators  
**Module:** Asset & Depreciation Management  
**Access Level:** Admin

## Quick Access URLs

### Main Screens

-   **Asset Register:** `/admin/finance/assets`
-   **Add New Asset:** `/admin/finance/assets/create`
-   **Asset Details:** `/admin/finance/assets/{id}` (replace {id} with asset ID)
-   **Edit Asset:** `/admin/finance/assets/{id}/edit`

## Test Scenario 1: Register a New Asset (5 minutes)

### Step 1: Navigate to Asset Creation

1. Go to `/admin/finance/assets`
2. Click **"Add New Asset"** button (top-right)

### Step 2: Fill Basic Information

```
Company: Select any active company
Asset Category: Select from dropdown (optional)
Asset Name: "Dell Laptop - Marketing Team"
Asset Type: Tangible
Asset Category: "Computer Equipment"
Description: "Dell XPS 15 for marketing department"
```

### Step 3: Enter Purchase Details

```
Purchase Cost: 150000.00
Purchase Date BS: 2081-08-15
Fiscal Year BS: 2081-2082
Serial Number: DXPS123456
Vendor Name: "Dell Nepal Pvt. Ltd."
Invoice Number: "INV-2081-001"
```

### Step 4: Configure Depreciation

```
Depreciation Method: Straight Line
Useful Life Years: 5
Useful Life Months: 0
Salvage Value: 15000.00
Depreciation Rate: (leave empty for straight line)
```

### Step 5: Set Location & Assignment

```
Location: "Kathmandu Office - 2nd Floor"
Assigned To: "Marketing Department"
```

### Step 6: Submit

-   Click **"Register Asset"**
-   You'll be redirected to asset register
-   Note the auto-generated asset number: **AST-2081-2082-000001**

**Expected Result:**

-   Asset appears in register with status **Active** (green badge)
-   Book Value = Purchase Cost = रू 150,000.00
-   Accumulated Depreciation = रू 0.00

## Test Scenario 2: Calculate Monthly Depreciation (3 minutes)

### Step 1: Open Asset Details

1. From asset register, click **"View"** on the laptop asset
2. Note Financial Summary card showing:
    - Purchase Cost: रू 150,000.00
    - Accumulated Depreciation: रू 0.00
    - Current Book Value: रू 150,000.00

### Step 2: Calculate First Month Depreciation

1. Click **"Calculate Depreciation"** button (right sidebar)
2. Fill modal form:
    ```
    Fiscal Year: 2081-2082
    Fiscal Month: 08
    ```
3. Click **"Calculate"**

### Step 3: Verify Calculation

**Expected Calculation:**

-   Total Depreciable: रू 150,000 - रू 15,000 = रू 135,000
-   Useful Life: 5 years = 60 months
-   Monthly Depreciation: रू 135,000 / 60 = रू 2,250

**Check:**

-   Depreciation Schedule table now shows 1 row:

    -   Fiscal Year: 2081-2082
    -   Month: 08
    -   Opening Book Value: रू 150,000.00
    -   Depreciation: रू 2,250.00
    -   Accumulated Depreciation: रू 2,250.00
    -   Closing Book Value: रू 147,750.00
    -   Status: Posted (green)

-   Financial Summary updated:
    -   Purchase Cost: रू 150,000.00 (unchanged)
    -   Accumulated Depreciation: रू 2,250.00
    -   Current Book Value: रू 147,750.00

### Step 4: Try Duplicate Prevention

1. Click "Calculate Depreciation" again
2. Enter same fiscal year/month: 2081-2082, 08
3. Click "Calculate"

**Expected Result:** Error message "Depreciation already calculated for this period."

## Test Scenario 3: Transfer Asset Between Companies (4 minutes)

### Prerequisites

-   At least 2 active companies in system
-   Asset status must be "Active"

### Step 1: Initiate Transfer

1. Open asset details page
2. Click **"Transfer Asset"** button (right sidebar)
3. Modal opens

### Step 2: Fill Transfer Details

```
To Company: Select different company from dropdown
Transfer Date BS: 2081-09-01
Notes: "Transferred to Pokhara branch for regional marketing"
```

### Step 3: Complete Transfer

1. Click **"Transfer"** button
2. Redirected to asset register

**Expected Result:**

-   Asset status changed to **Transferred** (blue badge)
-   When viewing details:
    -   Status shows "Transferred"
    -   Transfer information populated
    -   Quick Actions buttons no longer visible (asset not active)

## Test Scenario 4: Dispose Asset (3 minutes)

### Prerequisites

-   Asset status must be "Active"
-   Create a new test asset if previous one was transferred

### Step 1: Initiate Disposal

1. Open active asset details
2. Click **"Dispose Asset"** button (red, right sidebar)
3. Modal opens

### Step 2: Fill Disposal Details

```
Disposal Date BS: 2081-10-15
Disposal Value: 25000.00
Reason: "Asset damaged beyond repair - water damage"
```

### Step 3: Complete Disposal

1. Click **"Dispose"** button
2. Redirected to asset register

**Expected Result:**

-   Asset status changed to **Disposed** (yellow/warning badge)
-   When viewing details:
    -   Status shows "Disposed"
    -   Disposal information visible
    -   Quick Actions buttons hidden

## Test Scenario 5: Filter & Search Assets (2 minutes)

### Test Filters

1. **Company Filter:**

    - Select specific company from dropdown
    - Click "Filter"
    - Only assets from that company displayed

2. **Status Filter:**

    - Select "Active" from status dropdown
    - Click "Filter"
    - Only active assets shown

3. **Asset Type Filter:**

    - Select "Tangible"
    - Click "Filter"
    - Only tangible assets shown

4. **Search Box:**

    - Enter "Laptop" in search box
    - Click "Filter"
    - Assets matching name/number/serial shown

5. **Combined Filters:**

    - Select company + status + type + search
    - Click "Filter"
    - All filters applied together

6. **Clear Filters:**
    - Navigate to `/admin/finance/assets`
    - All assets displayed again

## Test Scenario 6: Edit Asset Information (3 minutes)

### Editable Fields Test

1. **Open Edit Form:**

    - From asset register, click "Edit" button
    - Edit form loads

2. **Update Fields:**

    ```
    Location: "Pokhara Office - 3rd Floor"
    Assigned To: "Sales Department"
    Status: Under Maintenance
    ```

3. **Verify Locked Fields:**

    - Notice info alert at top
    - Purchase Cost field NOT present
    - Depreciation settings NOT editable
    - Purchase dates NOT editable

4. **Submit Changes:**
    - Click "Update Asset"
    - Redirected to register
    - Click "View" to verify changes

**Expected Result:**

-   Location updated to Pokhara
-   Assigned To changed to Sales
-   Status badge shows "Under Maintenance" (blue)
-   Purchase/depreciation data unchanged

## Expected Asset Number Sequence

Assets created in fiscal year 2081-2082:

```
1st asset: AST-2081-2082-000001
2nd asset: AST-2081-2082-000002
3rd asset: AST-2081-2082-000003
...
```

Assets created in fiscal year 2082-2083:

```
1st asset: AST-2082-2083-000001
2nd asset: AST-2082-2083-000002
...
```

**Note:** Sequence resets each fiscal year.

## Depreciation Calculation Details

### Straight Line Method

```
Formula: (Purchase Cost - Salvage Value) / Total Months

Example:
Purchase Cost: रू 150,000
Salvage Value: रू 15,000
Useful Life: 5 years = 60 months
Depreciable Amount: रू 135,000
Monthly Depreciation: रू 135,000 / 60 = रू 2,250
```

### Book Value Protection

```
Book Value NEVER goes below Salvage Value

If calculated book value < salvage value:
  Final Book Value = Salvage Value
```

## Status Lifecycle

```
Purchase → Active
         ↓
    (Calculate Depreciation)
         ↓
    Still Active
         ↓
    (One of these actions)
         ↓
    ├── Disposed
    ├── Sold
    ├── Transferred
    ├── Under Maintenance
    └── Written Off
```

## Common Validation Errors

1. **"The company_id field is required"**

    - Fix: Select a company from dropdown

2. **"The purchase_cost must be at least 0"**

    - Fix: Enter positive number

3. **"Depreciation already calculated for this period"**

    - Fix: Choose different fiscal month or skip

4. **"The to_company_id field must be different"**
    - Fix: Select different company for transfer

## Quick Reference: Depreciation Methods

| Method                  | Description               | When to Use                                      |
| ----------------------- | ------------------------- | ------------------------------------------------ |
| **Straight Line**       | Equal amounts each period | Most common, general assets                      |
| **Declining Balance**   | Percentage of book value  | Technology, vehicles (higher early depreciation) |
| **Sum of Years**        | Accelerated depreciation  | Assets losing value quickly                      |
| **Units of Production** | Based on usage            | Manufacturing equipment                          |
| **None**                | No depreciation           | Land, intangible assets                          |

**Currently Implemented:** Straight Line (others framework-ready)

## Asset Type Guide

### Tangible Assets

-   Computer Equipment
-   Furniture & Fixtures
-   Vehicles
-   Machinery
-   Buildings
-   Office Equipment

### Intangible Assets

-   Software Licenses
-   Patents
-   Trademarks
-   Copyrights
-   Goodwill

## Performance Tips

1. **Use Filters:** Filter by company to reduce list size
2. **Search:** Use asset number for quick lookup
3. **Pagination:** Register shows 20 assets per page

## Troubleshooting

### Asset Number Not Generating?

-   Check fiscal_year_bs is properly formatted (YYYY-YYYY)
-   Verify company_id is valid

### Can't Calculate Depreciation?

-   Verify asset status is "Active"
-   Check no duplicate record exists for period
-   Ensure depreciation method is not "none"

### Transfer Not Working?

-   Verify target company exists and is active
-   Ensure target company is different from current
-   Check asset status is "Active"

### Depreciation Amount Seems Wrong?

-   Verify useful_life_years and months are set
-   Check salvage_value is less than purchase_cost
-   Confirm depreciation_method is "straight_line"

## Next Steps After Testing

1. **Seed More Assets:** Create various asset types
2. **Calculate Multiple Periods:** Post 3-4 months depreciation
3. **Test All Statuses:** Try each status transition
4. **Export Reports:** (Coming in Phase 3)
5. **Integration Testing:** (Coming in Phase 3 with Journal Entries)

---

**Estimated Testing Time:** 20-25 minutes for all scenarios  
**Support:** Check `/docs/PHASE_2_IMPLEMENTATION_COMPLETE.md` for technical details
