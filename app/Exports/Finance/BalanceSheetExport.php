<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class BalanceSheetExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
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
        $data[] = array($this->companyName, '', '');
        $data[] = array('Balance Sheet', '', '');
        $data[] = array('As of: ' . $this->report['as_of_date'], '', '');
        $data[] = array('', '', '');

        // Assets Section
        $data[] = array('ASSETS', '', '');
        $data[] = array('Current Assets', '', '');
        foreach ($this->report['assets']['current_assets'] as $asset) {
            $data[] = array('', $asset['account'], number_format($asset['balance'], 2));
        }
        $data[] = array('', '', '');

        $data[] = array('Fixed Assets', '', '');
        foreach ($this->report['assets']['fixed_assets'] as $asset) {
            $data[] = array('', $asset['account'], number_format($asset['balance'], 2));
        }
        $data[] = array('', '', '');

        if (!empty($this->report['assets']['other_assets'])) {
            $data[] = array('Other Assets', '', '');
            foreach ($this->report['assets']['other_assets'] as $asset) {
                $data[] = array('', $asset['account'], number_format($asset['balance'], 2));
            }
            $data[] = array('', '', '');
        }

        $data[] = array('TOTAL ASSETS', '', number_format($this->report['assets']['total_assets'], 2));
        $data[] = array('', '', '');

        // Liabilities Section
        $data[] = array('LIABILITIES', '', '');
        $data[] = array('Current Liabilities', '', '');
        foreach ($this->report['liabilities']['current_liabilities'] as $liability) {
            $data[] = array('', $liability['account'], number_format($liability['balance'], 2));
        }
        $data[] = array('', '', '');

        if (!empty($this->report['liabilities']['long_term_liabilities'])) {
            $data[] = array('Long-term Liabilities', '', '');
            foreach ($this->report['liabilities']['long_term_liabilities'] as $liability) {
                $data[] = array('', $liability['account'], number_format($liability['balance'], 2));
            }
            $data[] = array('', '', '');
        }

        $data[] = array('TOTAL LIABILITIES', '', number_format($this->report['liabilities']['total_liabilities'], 2));
        $data[] = array('', '', '');

        // Equity Section
        $data[] = array('EQUITY', '', '');
        foreach ($this->report['equity']['capital'] as $equity) {
            $data[] = array('', $equity['account'], number_format($equity['balance'], 2));
        }
        $data[] = array('', 'Retained Earnings', number_format($this->report['equity']['retained_earnings'], 2));
        $data[] = array('', '', '');
        $data[] = array('TOTAL EQUITY', '', number_format($this->report['equity']['total_equity'], 2));
        $data[] = array('', '', '');

        // Total
        $data[] = array('TOTAL LIABILITIES & EQUITY', '', number_format($this->report['total_liabilities_and_equity'], 2));

        // Balance Check
        $balanceStatus = $this->report['balance_check'] ? 'BALANCED ✓' : 'NOT BALANCED ✗';
        $data[] = array('', '', '');
        $data[] = array('Status: ' . $balanceStatus, '', '');

        return $data;
    }

    public function title(): string
    {
        return 'Balance Sheet';
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
            'B' => 40,
            'C' => 20,
        );
    }
}
