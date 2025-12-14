<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CashFlowExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
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

        $data[] = array($this->companyName, '', '');
        $data[] = array('Cash Flow Statement', '', '');
        $data[] = array('Period: ' . $this->report['period'], '', '');
        $data[] = array('', '', '');

        // Operating Activities
        $data[] = array('OPERATING ACTIVITIES', '', '');
        $data[] = array('Cash Inflows', '', '');
        foreach ($this->report['operating_activities']['inflows'] as $item) {
            $data[] = array('', $item['description'], number_format($item['amount'], 2));
        }
        $data[] = array('', '', '');
        $data[] = array('Cash Outflows', '', '');
        foreach ($this->report['operating_activities']['outflows'] as $item) {
            $data[] = array('', $item['description'], number_format($item['amount'], 2));
        }
        $data[] = array('', 'Net Operating Cash Flow', number_format($this->report['operating_activities']['net'], 2));
        $data[] = array('', '', '');

        // Investing Activities
        $data[] = array('INVESTING ACTIVITIES', '', '');
        if (!empty($this->report['investing_activities']['inflows'])) {
            $data[] = array('Cash Inflows', '', '');
            foreach ($this->report['investing_activities']['inflows'] as $item) {
                $data[] = array('', $item['description'], number_format($item['amount'], 2));
            }
            $data[] = array('', '', '');
        }
        if (!empty($this->report['investing_activities']['outflows'])) {
            $data[] = array('Cash Outflows', '', '');
            foreach ($this->report['investing_activities']['outflows'] as $item) {
                $data[] = array('', $item['description'], number_format($item['amount'], 2));
            }
        }
        $data[] = array('', 'Net Investing Cash Flow', number_format($this->report['investing_activities']['net'], 2));
        $data[] = array('', '', '');

        // Financing Activities
        $data[] = array('FINANCING ACTIVITIES', '', '');
        if (!empty($this->report['financing_activities']['inflows'])) {
            $data[] = array('Cash Inflows', '', '');
            foreach ($this->report['financing_activities']['inflows'] as $item) {
                $data[] = array('', $item['description'], number_format($item['amount'], 2));
            }
            $data[] = array('', '', '');
        }
        if (!empty($this->report['financing_activities']['outflows'])) {
            $data[] = array('Cash Outflows', '', '');
            foreach ($this->report['financing_activities']['outflows'] as $item) {
                $data[] = array('', $item['description'], number_format($item['amount'], 2));
            }
        }
        $data[] = array('', 'Net Financing Cash Flow', number_format($this->report['financing_activities']['net'], 2));
        $data[] = array('', '', '');

        // Summary
        $data[] = array('CASH FLOW SUMMARY', '', '');
        $data[] = array('', 'Opening Cash Balance', number_format($this->report['opening_cash_balance'], 2));
        $data[] = array('', 'Net Cash Flow', number_format($this->report['net_cash_flow'], 2));
        $data[] = array('', 'Closing Cash Balance', number_format($this->report['closing_cash_balance'], 2));

        return $data;
    }

    public function title(): string
    {
        return 'Cash Flow';
    }

    public function styles(Worksheet $sheet)
    {
        return array(
            1 => array('font' => array('bold' => true, 'size' => 16), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            2 => array('font' => array('bold' => true, 'size' => 14), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            3 => array('font' => array('italic' => true, 'size' => 11), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            5 => array('font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'FFFFFF')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => '4472C4'))),
        );
    }

    public function columnWidths(): array
    {
        return array('A' => 5, 'B' => 40, 'C' => 20);
    }
}
