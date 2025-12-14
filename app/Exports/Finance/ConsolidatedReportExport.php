<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ConsolidatedReportExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $report;

    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function array(): array
    {
        $data = array();

        $data[] = array('Consolidated Financial Report', '', '', '', '');
        $data[] = array('Fiscal Year: ' . $this->report['fiscal_year'], '', '', '', '');
        $data[] = array('', '', '', '', '');

        $data[] = array('Company', 'Revenue', 'Expenses', 'Net Profit', 'Profit Margin');

        foreach ($this->report['companies'] as $company) {
            $data[] = array(
                $company['company'],
                number_format($company['revenue'], 2),
                number_format($company['expenses'], 2),
                number_format($company['net_profit'], 2),
                $company['profit_margin'] . '%'
            );
        }

        $data[] = array('', '', '', '', '');
        $data[] = array(
            'GROUP TOTALS',
            number_format($this->report['group_totals']['total_revenue'], 2),
            number_format($this->report['group_totals']['total_expenses'], 2),
            number_format($this->report['group_totals']['total_net_profit'], 2),
            number_format($this->report['group_totals']['average_profit_margin'], 2) . '%'
        );

        return $data;
    }

    public function title(): string
    {
        return 'Consolidated Report';
    }

    public function styles(Worksheet $sheet)
    {
        return array(
            1 => array('font' => array('bold' => true, 'size' => 16), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            2 => array('font' => array('italic' => true, 'size' => 12), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            4 => array('font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => 'FFFFFF')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => '4472C4'))),
        );
    }

    public function columnWidths(): array
    {
        return array('A' => 35, 'B' => 20, 'C' => 20, 'D' => 20, 'E' => 15);
    }
}
