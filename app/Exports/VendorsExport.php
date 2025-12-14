<?php

namespace App\Exports;

use App\Models\FinanceVendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VendorsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $vendorIds;

    public function __construct($vendorIds = null)
    {
        $this->vendorIds = $vendorIds;
    }

    public function collection()
    {
        $query = FinanceVendor::with('company');

        if ($this->vendorIds) {
            $query->whereIn('id', $this->vendorIds);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Vendor Code',
            'Vendor Name',
            'Vendor Type',
            'Company',
            'Contact Person',
            'Email',
            'Phone',
            'PAN Number',
            'Address',
            'Status',
            'Created At',
        ];
    }

    public function map($vendor): array
    {
        return [
            $vendor->vendor_code,
            $vendor->vendor_name,
            ucfirst(str_replace('_', ' ', $vendor->vendor_type)),
            $vendor->company->company_name,
            $vendor->contact_person,
            $vendor->email,
            $vendor->contact_number,
            $vendor->pan_number,
            $vendor->address,
            $vendor->is_active ? 'Active' : 'Inactive',
            $vendor->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
