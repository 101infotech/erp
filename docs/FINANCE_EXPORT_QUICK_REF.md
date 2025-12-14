# Finance Reports - PDF & Excel Export Quick Reference

## Export Endpoints

### PDF Exports

All PDF endpoints return downloadable PDF files with professional branding.

#### Profit & Loss PDF

```
GET /api/v1/finance/reports/profit-loss/pdf
```

**Parameters**:

-   `company_id` (required): Company ID
-   `fiscal_year` (required): Fiscal year (e.g., 2081)
-   `month` (optional): Month number (1-12)

**Example**:

```bash
curl -H "Authorization: Bearer {token}" \
  "/api/v1/finance/reports/profit-loss/pdf?company_id=1&fiscal_year=2081&month=1"
```

#### Balance Sheet PDF

```
GET /api/v1/finance/reports/balance-sheet/pdf
```

**Parameters**:

-   `company_id` (required): Company ID
-   `as_of_date` (required): Date in BS format (YYYY-MM-DD)

**Example**:

```bash
curl -H "Authorization: Bearer {token}" \
  "/api/v1/finance/reports/balance-sheet/pdf?company_id=1&as_of_date=2081-03-31"
```

#### Cash Flow PDF

```
GET /api/v1/finance/reports/cash-flow/pdf
```

**Parameters**:

-   `company_id` (required): Company ID
-   `fiscal_year` (required): Fiscal year
-   `month` (optional): Month number (1-12)

#### Trial Balance PDF

```
GET /api/v1/finance/reports/trial-balance/pdf
```

**Parameters**:

-   `company_id` (required): Company ID
-   `as_of_date` (required): Date in BS format

#### Expense Summary PDF

```
GET /api/v1/finance/reports/expense-summary/pdf
```

**Parameters**:

-   `company_id` (required): Company ID
-   `fiscal_year` (required): Fiscal year

#### Consolidated Report PDF

```
GET /api/v1/finance/reports/consolidated/pdf
```

**Parameters**:

-   `fiscal_year` (required): Fiscal year

---

### Excel Exports

All Excel endpoints return downloadable .xlsx files with formatted spreadsheets.

#### Profit & Loss Excel

```
GET /api/v1/finance/reports/profit-loss/excel
```

**Parameters**: Same as PDF version

**Example**:

```bash
curl -H "Authorization: Bearer {token}" \
  "/api/v1/finance/reports/profit-loss/excel?company_id=1&fiscal_year=2081" \
  -o report.xlsx
```

#### Balance Sheet Excel

```
GET /api/v1/finance/reports/balance-sheet/excel
```

**Features**:

-   Styled headers (blue background)
-   Auto-sized columns
-   Number formatting with comma separators
-   Balance verification indicator

#### Cash Flow Excel

```
GET /api/v1/finance/reports/cash-flow/excel
```

**Features**:

-   Three activity sections (Operating, Investing, Financing)
-   Cash flow summary
-   Color-coded amounts

#### Trial Balance Excel

```
GET /api/v1/finance/reports/trial-balance/excel
```

**Features**:

-   Account-level detail
-   Debit/Credit columns
-   Balance verification
-   Grand totals

#### Expense Summary Excel

```
GET /api/v1/finance/reports/expense-summary/excel
```

**Features**:

-   **Multiple Sheets**:
    -   Sheet 1: Monthly Breakdown (12 months)
    -   Sheet 2: Category Analysis (top categories)
-   Charts (if enabled)
-   Conditional formatting

#### Consolidated Report Excel

```
GET /api/v1/finance/reports/consolidated/excel
```

**Features**:

-   All companies in single table
-   Group totals
-   Profit margin calculations

---

## Frontend Integration

### JavaScript/Fetch Example

```javascript
// Download PDF Report
async function downloadProfitLossPdf(companyId, fiscalYear, month = null) {
    const params = new URLSearchParams({
        company_id: companyId,
        fiscal_year: fiscalYear,
        ...(month && { month }),
    });

    const response = await fetch(
        `/api/v1/finance/reports/profit-loss/pdf?${params}`,
        {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }
    );

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `ProfitLoss_${fiscalYear}.pdf`;
    a.click();
}

// Download Excel Report
async function downloadBalanceSheetExcel(companyId, asOfDate) {
    const params = new URLSearchParams({
        company_id: companyId,
        as_of_date: asOfDate,
    });

    const response = await fetch(
        `/api/v1/finance/reports/balance-sheet/excel?${params}`,
        {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        }
    );

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `BalanceSheet_${asOfDate}.xlsx`;
    a.click();
}
```

### React Example

```jsx
const ExportButtons = ({ companyId, fiscalYear }) => {
    const downloadReport = async (format) => {
        const endpoint = `/api/v1/finance/reports/profit-loss/${format}`;
        const params = `?company_id=${companyId}&fiscal_year=${fiscalYear}`;

        const response = await fetch(endpoint + params, {
            headers: { Authorization: `Bearer ${token}` },
        });

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `ProfitLoss_${fiscalYear}.${format}`;
        a.click();
    };

    return (
        <div>
            <button onClick={() => downloadReport("pdf")}>Download PDF</button>
            <button onClick={() => downloadReport("excel")}>
                Download Excel
            </button>
        </div>
    );
};
```

### Vue Example

```vue
<template>
    <div class="export-buttons">
        <button @click="exportReport('pdf')" class="btn-pdf">
            <i class="fa fa-file-pdf"></i> Export PDF
        </button>
        <button @click="exportReport('excel')" class="btn-excel">
            <i class="fa fa-file-excel"></i> Export Excel
        </button>
    </div>
</template>

<script>
export default {
    props: ["companyId", "fiscalYear", "reportType"],
    methods: {
        async exportReport(format) {
            const endpoint = `/api/v1/finance/reports/${this.reportType}/${format}`;
            const params = new URLSearchParams({
                company_id: this.companyId,
                fiscal_year: this.fiscalYear,
            });

            try {
                const response = await this.$http.get(`${endpoint}?${params}`, {
                    responseType: "blob",
                });

                const url = window.URL.createObjectURL(response.data);
                const link = document.createElement("a");
                link.href = url;
                link.download = `${this.reportType}_${this.fiscalYear}.${format}`;
                link.click();
            } catch (error) {
                console.error("Export failed:", error);
            }
        },
    },
};
</script>
```

---

## File Naming Convention

### PDF Files

```
{CompanyName}_{ReportType}_{Period}_{Date}.pdf
```

**Examples**:

-   `ABC_Pvt_Ltd_ProfitLoss_2081_M01_20241211.pdf`
-   `ABC_Pvt_Ltd_BalanceSheet_2081-03-31_20241211.pdf`
-   `ConsolidatedReport_FY2081_20241211.pdf`

### Excel Files

```
{CompanyName}_{ReportType}_{Period}_{Date}.xlsx
```

**Examples**:

-   `ABC_Pvt_Ltd_ProfitLoss_2081_20241211.xlsx`
-   `ABC_Pvt_Ltd_ExpenseSummary_2081_20241211.xlsx`

---

## Response Headers

### PDF Downloads

```
Content-Type: application/pdf
Content-Disposition: attachment; filename="Report.pdf"
```

### Excel Downloads

```
Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
Content-Disposition: attachment; filename="Report.xlsx"
```

---

## Error Handling

### Validation Errors (422)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "company_id": ["The company id field is required."]
    }
}
```

### Not Found (404)

```json
{
    "message": "Company not found"
}
```

### Server Error (500)

```json
{
    "message": "Failed to generate report",
    "error": "Error details..."
}
```

---

## Styling & Branding

### PDF Appearance

-   **Header**: Company logo + name
-   **Colors**: Professional blue (#4472C4)
-   **Font**: DejaVu Sans (supports Unicode)
-   **Layout**: A4 portrait/landscape
-   **Sections**: Color-coded headers
-   **Totals**: Highlighted with borders

### Excel Appearance

-   **Headers**: Blue background, white text
-   **Data**: Alternating row colors
-   **Numbers**: Formatted with 2 decimals
-   **Columns**: Auto-sized
-   **Borders**: Professional styling

---

## Performance Tips

### 1. Cache Generated Reports

```php
$cacheKey = "pdf_{$reportType}_{$companyId}_{$period}";
return Cache::remember($cacheKey, 3600, function() {
    return $this->pdfService->export(...);
});
```

### 2. Queue Large Reports

```php
dispatch(new GenerateReportJob($reportType, $params))->onQueue('reports');
```

### 3. Async Downloads

```javascript
// Show loading indicator
showLoading();

// Download in background
downloadReport(params)
    .then(() => hideLoading())
    .catch((error) => showError(error));
```

### 4. Compression

Enable gzip compression in nginx/Apache for faster downloads.

---

## Testing

### cURL Examples

#### Test PDF Export

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost/api/v1/finance/reports/profit-loss/pdf?company_id=1&fiscal_year=2081" \
  -o test.pdf

# Verify file
file test.pdf
# Output: test.pdf: PDF document, version 1.4
```

#### Test Excel Export

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  "http://localhost/api/v1/finance/reports/balance-sheet/excel?company_id=1&as_of_date=2081-03-31" \
  -o test.xlsx

# Verify file
file test.xlsx
# Output: test.xlsx: Microsoft Excel 2007+
```

### Postman Collection

Import these endpoints into Postman for easy testing:

1. Set base URL: `http://localhost/api/v1/finance/reports`
2. Add Authorization header: `Bearer {token}`
3. Set response type to "Blob" for downloads
4. Test all 12 export endpoints

---

## Summary

✅ **18 Report Endpoints**: 6 JSON + 6 PDF + 6 Excel
✅ **3 Dashboard Endpoints**: Full dashboard, KPIs, Revenue trends
✅ **Professional Branding**: Company info, colors, formatting
✅ **Multiple Formats**: View online (JSON) or download (PDF/Excel)
✅ **Authenticated**: All routes protected with Sanctum
✅ **Optimized**: Efficient file generation and delivery

**Total Finance Endpoints: 60**

-   Phase 2 (Transactions): 39 endpoints
-   Phase 3 (Reports): 21 endpoints (9 JSON + 12 exports)
