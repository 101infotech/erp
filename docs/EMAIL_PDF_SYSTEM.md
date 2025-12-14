# Email & PDF System Documentation

## Overview

The HRM system includes automated email notifications and PDF payslip generation when payrolls are approved. This document covers the implementation, configuration, and usage of the email and PDF system.

## Components

### 1. PDF Generation Service

**File:** `app/Services/PayslipPdfService.php`

#### Features:

-   Generates professional PDF payslips using DomPDF
-   Includes company branding and employee information
-   Shows detailed salary breakdown (earnings and deductions)
-   Displays attendance summary
-   Lists any attendance anomalies
-   Includes approval information
-   Adds signatures section
-   Shows DRAFT watermark for non-approved payslips

#### Methods:

**`generatePayslipPdf(HrmPayrollRecord $payrollRecord): string`**

-   Generates PDF for a payroll record
-   Returns the absolute file path to the generated PDF
-   Stores PDF in `storage/app/payslips/` directory
-   Automatically loads all necessary relationships

**`deletePayslipPdf(string $path): bool`**

-   Deletes a payslip PDF file
-   Returns true if successful

**`getPayslipUrl(string $path): ?string`**

-   Gets the public URL for a payslip PDF
-   Returns null if file doesn't exist

#### Usage Example:

```php
use App\Services\PayslipPdfService;
use App\Models\HrmPayrollRecord;

$pdfService = app(PayslipPdfService::class);
$payroll = HrmPayrollRecord::find(1);

// Generate PDF
$pdfPath = $pdfService->generatePayslipPdf($payroll);

// The PDF is now stored at $pdfPath
// Example: /path/to/storage/app/payslips/payslip_1_2081_01_01_1.pdf
```

### 2. Email Notification

**File:** `app/Mail/PayrollApprovedMail.php`

#### Features:

-   Sends beautiful HTML email to employee
-   Includes payslip summary (period, net salary, approved by)
-   Attaches PDF payslip automatically
-   Uses company branding (lime/slate theme)
-   Mobile-responsive design

#### Email Template:

**File:** `resources/views/emails/payroll-approved.blade.php`

The email includes:

-   Greeting with employee name
-   Approval notification
-   Summary table with key information
-   Important notes section
-   PDF attachment
-   Professional footer

#### Usage Example:

```php
use App\Mail\PayrollApprovedMail;
use Illuminate\Support\Facades\Mail;

Mail::to($employee->email)
    ->send(new PayrollApprovedMail($payroll, $pdfPath));
```

### 3. PDF Template

**File:** `resources/views/pdfs/payslip.blade.php`

#### Layout Sections:

1. **Header**: Company name, address, period
2. **Employee Information**: Name, code, department, designation, PAN, bank account
3. **Attendance Summary**: All attendance metrics in a grid
4. **Salary Breakdown**: Earnings and deductions table
5. **Anomalies**: List of any attendance issues
6. **Approval Information**: Who approved and when
7. **Signatures**: Employee and authorized signature lines
8. **Footer**: Generation timestamp and notes

#### Styling:

-   A4 portrait layout
-   Professional fonts (DejaVu Sans)
-   Clean table layouts
-   Color-coded anomalies (critical=red, major=orange, minor=yellow)
-   Status badges
-   DRAFT watermark for non-approved payslips

### 4. Controller Integration

**File:** `app/Http/Controllers/Admin/HrmPayrollController.php`

#### Automatic Generation on Approval:

The `approve()` method automatically:

1. Generates PDF payslip
2. Stores PDF path in database
3. Sends email notification to employee
4. Handles errors gracefully (approval doesn't fail if PDF/email fails)

```php
public function approve(Request $request, $id)
{
    // ... validation ...

    DB::transaction(function() use ($payroll) {
        $payroll->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approved_by_name' => auth()->user()->name,
        ]);

        try {
            $pdfPath = $this->pdfService->generatePayslipPdf($payroll);
            $payroll->update(['payslip_pdf_path' => $pdfPath]);

            if ($payroll->employee->email) {
                Mail::to($payroll->employee->email)
                    ->send(new PayrollApprovedMail($payroll, $pdfPath));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF or send email: ' . $e->getMessage());
        }
    });
}
```

#### Manual Download:

The `downloadPdf()` method allows downloading payslips:

-   Auto-generates PDF if not already created
-   Returns file download response
-   Uses friendly filename: `Payslip_2081_01_01.pdf`

Route: `GET /admin/hrm/payroll/{payroll}/download-pdf`

## Database Schema

### Migration: `add_payslip_pdf_path_to_hrm_payroll_records_table`

```php
Schema::table('hrm_payroll_records', function (Blueprint $table) {
    $table->string('payslip_pdf_path')->nullable()->after('transaction_reference');
    $table->string('approved_by_name')->nullable()->after('approved_by');
});
```

**Fields:**

-   `payslip_pdf_path`: Stores absolute file path to generated PDF
-   `approved_by_name`: Caches approver's name for PDF/email

## Configuration

### DomPDF Configuration

**File:** `config/dompdf.php`

Key settings:

-   `default_font`: 'DejaVu Sans' (supports Unicode)
-   `default_paper_size`: 'a4'
-   `orientation`: 'portrait'
-   `enable_remote`: false (security)
-   `enable_html5_parser`: true

### Mail Configuration

**File:** `config/mail.php`

Ensure mail driver is properly configured:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourcompany.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Storage Setup

### Directory Structure:

```
storage/
├── app/
│   └── payslips/
│       ├── payslip_1_2081_01_01_1.pdf
│       ├── payslip_2_2081_01_01_2.pdf
│       └── ...
```

### Permissions:

Ensure storage directory is writable:

```bash
chmod -R 775 storage/app/payslips
```

### Storage Link (if public access needed):

```bash
php artisan storage:link
```

## Frontend Integration

### Download Button in Payslip View

**File:** `resources/views/admin/hrm/payroll/show.blade.php`

```blade
@if($payroll->payslip_pdf_path || $payroll->status !== 'draft')
    <a href="{{ route('admin.hrm.payroll.download-pdf', $payroll->id) }}"
       class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Download PDF
    </a>
@endif
```

## Testing

### Test PDF Generation:

```bash
php artisan tinker

$payroll = \App\Models\HrmPayrollRecord::first();
$pdfService = app(\App\Services\PayslipPdfService::class);
$path = $pdfService->generatePayslipPdf($payroll);
echo $path;
```

### Test Email Sending:

```bash
php artisan tinker

$payroll = \App\Models\HrmPayrollRecord::with('employee')->first();
$pdfPath = storage_path('app/payslips/test.pdf');
\Mail::to('test@example.com')->send(new \App\Mail\PayrollApprovedMail($payroll, $pdfPath));
```

### Check Mail Queue:

```bash
php artisan queue:work
```

## Troubleshooting

### PDF Not Generating

**Issue:** PDF generation fails with memory error

**Solution:** Increase PHP memory limit

```php
// In config/dompdf.php
ini_set('memory_limit', '256M');
```

---

**Issue:** Fonts not displaying correctly

**Solution:** Use DejaVu Sans font (default) which supports Unicode

```php
// In PDF view
body {
    font-family: 'DejaVu Sans', sans-serif;
}
```

### Email Not Sending

**Issue:** Email not being sent

**Check:**

1. Verify `.env` mail configuration
2. Check if employee has email address
3. Review `storage/logs/laravel.log` for errors
4. Test mail configuration: `php artisan config:clear`

---

**Issue:** Email goes to spam

**Solutions:**

1. Use authenticated SMTP
2. Add SPF/DKIM records to domain
3. Use verified sender email
4. Avoid spam trigger words

### Storage Issues

**Issue:** Permission denied when saving PDF

**Solution:**

```bash
chmod -R 775 storage/app
chown -R www-data:www-data storage/app
```

---

**Issue:** Disk full error

**Solution:** Clean old payslips or increase disk space

```bash
# Remove payslips older than 1 year
find storage/app/payslips -name "*.pdf" -mtime +365 -delete
```

## Best Practices

### 1. Queue Email Sending

For better performance, queue email sending:

```php
Mail::to($employee->email)
    ->queue(new PayrollApprovedMail($payroll, $pdfPath));
```

Update `.env`:

```env
QUEUE_CONNECTION=database
```

Run queue worker:

```bash
php artisan queue:work
```

### 2. Validate Email Addresses

Before sending, validate employee email:

```php
if ($employee->email && filter_var($employee->email, FILTER_VALIDATE_EMAIL)) {
    Mail::to($employee->email)->send(...);
}
```

### 3. Archive Old Payslips

Create scheduled task to archive old payslips:

```php
// In app/Console/Kernel.php
$schedule->command('payslips:archive')->monthly();
```

### 4. Add PDF Encryption (Optional)

For sensitive data, encrypt PDFs:

```php
$pdf->setEncryption('password', 'owner-password');
```

### 5. Email Delivery Tracking

Track email delivery using webhooks or services like SendGrid, Mailgun.

## Future Enhancements

1. **Bulk PDF Generation**: Generate multiple payslips at once
2. **Email Templates**: Allow customization of email templates
3. **Delivery Status**: Track email delivery status
4. **Re-send Email**: Allow re-sending payslip email
5. **PDF Watermarks**: Add custom watermarks
6. **Multi-language Support**: Support multiple languages in PDF/email
7. **Digital Signatures**: Add digital signature support
8. **SMS Notifications**: Send SMS along with email
9. **Cloud Storage**: Store PDFs in S3/Google Cloud instead of local
10. **Preview Before Send**: Preview email/PDF before sending

## Security Considerations

1. **Access Control**: Only authorized users can download payslips
2. **File Storage**: PDFs stored outside public directory
3. **Email Encryption**: Use TLS for email transmission
4. **Data Privacy**: Include only necessary information in email
5. **Audit Trail**: Log all PDF generations and email sends
6. **Rate Limiting**: Prevent abuse of email/PDF generation
7. **Virus Scanning**: Scan generated PDFs (optional)
8. **Expiry**: Auto-delete old payslips after retention period

## Support

For issues or questions:

-   Check logs: `storage/logs/laravel.log`
-   Review error messages in browser console
-   Test with sample data first
-   Ensure all dependencies are installed
-   Verify file permissions

## Conclusion

The email and PDF system provides automated, professional payslip delivery to employees. It's fully integrated with the payroll approval workflow and requires minimal manual intervention. The system is designed to be reliable, secure, and easy to maintain.
