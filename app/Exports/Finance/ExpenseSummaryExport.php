<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExpenseSummaryExport implements WithMultipleSheets
{
    protected array $report;
    protected string $companyName;

    public function __construct(array $report, string $companyName)
    {
        $this->report = $report;
        $this->companyName = $companyName;
    }

    public function sheets(): array
    {
        return array(
            new MonthlyBreakdownSheet($this->report, $this->companyName),
            new CategoryAnalysisSheet($this->report, $this->companyName),
        );
    }
}

class MonthlyBreakdownSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
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
        $data[] = array('Expense Summary - Monthly Breakdown', '', '');
        $data[] = array('Fiscal Year: ' . $this->report['fiscal_year'], '', '');
        $data[] = array('', '', '');

        $data[] = array('Month', 'Month Name', 'Total Expenses');

        foreach ($this->report['monthly_breakdown'] as $month) {
            $data[] = array(
                $month['month'],
                $month['month_name'],
                number_format($month['total_expenses'], 2)
            );
        }

        $data[] = array('', '', '');
        $data[] = array('TOTAL', '', number_format($this->report['total_expenses'], 2));
        $data[] = array('AVERAGE', '', number_format($this->report['average_monthly_expense'], 2));

        return $data;
    }

    public function title(): string
    {
        return 'Monthly Breakdown';
    }

    public function styles(Worksheet $sheet)
    {
        return array(
            1 => array('font' => array('bold' => true, 'size' => 16), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            2 => array('font' => array('bold' => true, 'size' => 14), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            5 => array('font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => 'FFFFFF')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => '4472C4'))),
        );
    }

    public function columnWidths(): array
    {
        return array('A' => 10, 'B' => 20, 'C' => 20);
    }
}

class CategoryAnalysisSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
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

        $data[] = array($this->companyName, '', '', '');
        $data[] = array('Expense Summary - Category Analysis', '', '', '');
        $data[] = array('Fiscal Year: ' . $this->report['fiscal_year'], '', '', '');
        $data[] = array('', '', '', '');

        $data[] = array('Category', 'Total Amount', 'Percentage', 'Count');

        foreach ($this->report['category_analysis'] as $category) {
            $data[] = array(
                $category['category'],
                number_format($category['total'], 2),
                $category['percentage'] . '%',
                $category['count']
            );
        }

        $data[] = array('', '', '', '');
        $data[] = array('TOTAL', number_format($this->report['total_expenses'], 2), '100%', '');

        return $data;
    }

    public function title(): string
    {
        return 'Category Analysis';
    }

    public function styles(Worksheet $sheet)
    {
        return array(
            1 => array('font' => array('bold' => true, 'size' => 16), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            2 => array('font' => array('bold' => true, 'size' => 14), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)),
            5 => array('font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => 'FFFFFF')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'startColor' => array('rgb' => '4472C4'))),
        );
    }

    public function columnWidths(): array
    {
        return array('A' => 30, 'B' => 20, 'C' => 15, 'D' => 10);
    }
}
