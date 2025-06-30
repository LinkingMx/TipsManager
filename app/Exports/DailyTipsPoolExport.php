<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DailyTipsPoolExport implements WithMultipleSheets
{
    use Exportable;

    protected $summary;

    protected $tipsData;

    protected $selectedDate;

    public function __construct($summary, $tipsData, $selectedDate)
    {
        $this->summary = $summary;
        $this->tipsData = $tipsData;
        $this->selectedDate = $selectedDate;
    }

    public function sheets(): array
    {
        return [
            'Summary' => new DailyTipsPoolSummarySheet($this->summary, $this->selectedDate),
            'Employee Details' => new DailyTipsPoolDetailsSheet($this->tipsData),
        ];
    }
}

class DailyTipsPoolSummarySheet implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected $summary;

    protected $selectedDate;

    public function __construct($summary, $selectedDate)
    {
        $this->summary = $summary;
        $this->selectedDate = $selectedDate;
    }

    public function array(): array
    {
        return [
            ['Report Date', \Carbon\Carbon::parse($this->selectedDate)->format('F j, Y')],
            ['Total Employees', $this->summary['total_employees']],
            ['AM Employees', $this->summary['am_employees']],
            ['PM Employees', $this->summary['pm_employees']],
            ['Total Points', number_format($this->summary['total_points'], 2)],
            ['AM Points', number_format($this->summary['am_total_points'], 2)],
            ['PM Points', number_format($this->summary['pm_total_points'], 2)],
            ['Total Tips Amount', '$'.number_format($this->summary['total_tips_amount'], 2)],
            ['AM Tips Amount', '$'.number_format($this->summary['am_tips_amount'], 2)],
            ['PM Tips Amount', '$'.number_format($this->summary['pm_tips_amount'], 2)],
            ['AM Tip Per Point', '$'.number_format($this->summary['am_tip_per_point'], 2)],
            ['PM Tip Per Point', '$'.number_format($this->summary['pm_tip_per_point'], 2)],
        ];
    }

    public function headings(): array
    {
        return [
            'Metric',
            'Value',
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD'],
                ],
            ],
            'A:B' => [
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ],
            'A1:B12' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}

class DailyTipsPoolDetailsSheet implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected $tipsData;

    public function __construct($tipsData)
    {
        $this->tipsData = $tipsData;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->tipsData as $row) {
            $data[] = [
                $row['employee_name'],
                $row['job_title'],
                $row['shift'] ?? '-',
                $row['in_date'] ? \Carbon\Carbon::parse($row['in_date'])->format('m/d/Y H:i') : '-',
                $row['out_date'] ? \Carbon\Carbon::parse($row['out_date'])->format('m/d/Y H:i') : '-',
                number_format($row['unpaid_break_time'], 2).'h',
                number_format($row['hours_worked'], 2).'h',
                number_format($row['job_position_points'], 2),
                number_format($row['calculated_points'], 2),
                $row['qualifies_for_full_points'] ? 'Full Points' : 'Proportional',
                $row['percentage'].'%',
                '$'.number_format($row['tip_amount'], 2),
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Position',
            'Shift',
            'In Date',
            'Out Date',
            'Unpaid Break',
            'Hours Worked',
            'Base Points',
            'Calculated Points',
            'Status',
            'Percentage',
            'Tip Amount',
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E8'],
                ],
            ],
            'A:L' => [
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
            'A:B' => [
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }

    public function title(): string
    {
        return 'Employee Details';
    }
}
