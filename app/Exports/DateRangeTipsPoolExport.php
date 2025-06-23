<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DateRangeTipsPoolExport implements WithMultipleSheets
{
    use Exportable;

    protected $summary;

    protected $tipsData;

    protected $dailyBreakdown;

    public function __construct($summary, $tipsData, $dailyBreakdown)
    {
        $this->summary = $summary;
        $this->tipsData = $tipsData;
        $this->dailyBreakdown = $dailyBreakdown;
    }

    public function sheets(): array
    {
        return [
            'Summary' => new SummarySheet($this->summary),
            'Tips Distribution' => new TipsDistributionSheet($this->tipsData),
            'Daily Breakdown' => new DailyBreakdownSheet($this->dailyBreakdown),
        ];
    }
}

class SummarySheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $summary;

    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    public function array(): array
    {
        return [
            ['Date Range', $this->summary['date_range']],
            ['Total Days', $this->summary['total_days']],
            ['Total Employees', $this->summary['total_employees']],
            ['Total Points', number_format($this->summary['total_points'], 2)],
            ['Total Tips Amount', '$'.number_format($this->summary['total_tips_amount'], 2)],
            ['Tip Per Point', '$'.number_format($this->summary['tip_per_point'], 2)],
            ['Average Tips Per Day', '$'.number_format($this->summary['avg_tips_per_day'], 2)],
        ];
    }

    public function headings(): array
    {
        return [
            'Metric',
            'Value',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD'],
                ],
            ],
            'A:B' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'A1:B8' => [
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

class TipsDistributionSheet implements FromArray, WithHeadings, WithStyles, WithTitle
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
                number_format($row['total_hours'], 2),
                number_format($row['avg_hours_per_day'], 2),
                $row['days_worked'],
                number_format($row['job_position_points'], 2),
                number_format($row['total_calculated_points'], 2),
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
            'Total Hours',
            'Avg Hours Per Day',
            'Days Worked',
            'Base Points',
            'Calculated Points',
            'Tip Amount',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E8'],
                ],
            ],
            'A:H' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'A:B' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }

    public function title(): string
    {
        return 'Tips Distribution';
    }
}

class DailyBreakdownSheet implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $dailyBreakdown;

    public function __construct($dailyBreakdown)
    {
        $this->dailyBreakdown = $dailyBreakdown;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->dailyBreakdown as $day) {
            // Add day header
            $data[] = [
                'DATE: '.\Carbon\Carbon::parse($day['date'])->format('F j, Y'),
                'Employees: '.$day['total_employees'],
                'Points: '.number_format($day['total_points'], 2),
                'Tips: $'.number_format($day['total_tips'], 2),
                '',
            ];

            // Add employee details for this day
            foreach ($day['employees'] as $employee) {
                $data[] = [
                    '  '.$employee['employee_name'],
                    $employee['job_title'],
                    number_format($employee['hours_worked'], 2).'h',
                    number_format($employee['calculated_points'], 2),
                    '',
                ];
            }

            // Add empty row for separation
            $data[] = ['', '', '', '', ''];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Employee / Date',
            'Position / Summary',
            'Hours / Points',
            'Points / Tips',
            '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3E0'],
                ],
            ],
            'A:E' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }

    public function title(): string
    {
        return 'Daily Breakdown';
    }
}
