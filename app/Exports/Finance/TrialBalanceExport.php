<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TrialBalanceExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
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

        $data[] = array($this->companyName, '', '', '', '');
        $data[] = array('Trial Balance', '', '', '', '');
        $data[] = array('As of: ' . $this->report['as_of_date'], '', '', '', '');
        $data[] = array('', '', '', '', '');

        // Headers
        $data[] = array('Account', 'Account Type', 'Debit', 'Credit', 'Balance');

        // Accounts
        foreach ($this->report['accounts'] as $account) {
            $data[] = array(
                $account['account'],
                ucfirst($account['account_type']),
                number_format($account['debit'], 2),
                number_format($account['credit'], 2),
                number_format($account['balance'], 2)
            );
        }

        $data[] = array('', '', '', '', '');

        // Totals
        $data[] = array(
            'TOTALS',
            '',
            number_format($this->report['totals']['total_debits'], 2),
            number_format($this->report['totals']['total_credits'], 2),
            number_format($this->report['totals']['difference'], 2)
        );

        // Balance Status
        $balanceStatus = $this->report['balanced'] ? 'BALANCED ✓' : 'NOT BALANCED ✗';
        $data[] = array('', '', '', '', '');
        $data[] = array('Status: ' . $balanceStatus, '', '', '', '');

        return $data;
    }

    public function title(): string
    {
        return 'Trial Balance';
    }

    public function styles(Worksheet $sheet)
    {
        return array(
            1 => array('font' => array('bold' => true, 'size' => 16), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            2 => array('font' => array('bold' => true, 'size' => 14), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            3 => array('font' => array('italic' => true, 'size' => 11), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            5 => array('font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => 'FFFFFF')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => '4472C4'))),
        );
    }

    public function columnWidths(): array
    {
        return array('A' => 35, 'B' => 15, 'C' => 18, 'D' => 18, 'E' => 18);
    }
}
