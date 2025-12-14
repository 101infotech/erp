<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProfitLossExport implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    protected array $report;
    protected string $companyName;

    public function __construct(array $report, string $companyName)
    {
        $this->report = $report;
        $this->companyName = $companyName;
    }

    public function array(): array
    {
        $data = array();

        // Header
        $data[] = array($this->companyName, '', '', '');
        $data[] = array('Profit & Loss Statement', '', '', '');
        $data[] = array('Period: ' . $this->report['period'], '', '', '');
        $data[] = array('', '', '', '');

        // Revenue Section
        $data[] = array('REVENUE', '', '', '');
        foreach ($this->report['revenue']['by_category'] as $item) {
            $data[] = array('', $item['category'], number_format($item['amount'], 2), $item['percentage'] . '%');
        }
        $data[] = array('', 'Total Revenue', number_format($this->report['revenue']['total'], 2), '100%');
        $data[] = array('', '', '', '');

        // Expenses Section
        $data[] = array('EXPENSES', '', '', '');
        foreach ($this->report['expenses']['by_category'] as $item) {
            $data[] = array('', $item['category'], number_format($item['amount'], 2), $item['percentage'] . '%');
        }
        $data[] = array('', 'Total Expenses', number_format($this->report['expenses']['total'], 2), '100%');
        $data[] = array('', '', '', '');

        // Net Profit
        $data[] = array('NET PROFIT', '', number_format($this->report['net_profit'], 2), $this->report['profit_margin'] . '%');

        return $data;
    }

    public function title(): string
    {
        return 'Profit & Loss';
    }

    public function headings(): array
    {
        return array('', 'Category', 'Amount (NPR)', 'Percentage');
    }

    public function styles(Worksheet $sheet)
    {
        return array(
            1 => array(
                'font' => array('bold' => true, 'size' => 16),
                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER),
            ),
            2 => array(
                'font' => array('bold' => true, 'size' => 14),
                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER),
            ),
            3 => array(
                'font' => array('italic' => true, 'size' => 11),
                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER),
            ),
            5 => array(
                'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')),
                'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => '4472C4')),
            ),
        );
    }

    public function columnWidths(): array
    {
        return array(
            'A' => 5,
            'B' => 35,
            'C' => 20,
            'D' => 15,
        );
    }
}
