<?php

namespace App\Exports;

use App\Models\FinanceCustomer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $customerIds;

    public function __construct($customerIds = null)
    {
        $this->customerIds = $customerIds;
    }

    public function collection()
    {
        $query = FinanceCustomer::with('company');

        if ($this->customerIds) {
            $query->whereIn('id', $this->customerIds);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Customer Code',
            'Customer Name',
            'Customer Type',
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

    public function map($customer): array
    {
        return [
            $customer->customer_code,
            $customer->customer_name,
            ucfirst($customer->customer_type),
            $customer->company->company_name,
            $customer->contact_person,
            $customer->email,
            $customer->contact_number,
            $customer->pan_number,
            $customer->address,
            $customer->is_active ? 'Active' : 'Inactive',
            $customer->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
