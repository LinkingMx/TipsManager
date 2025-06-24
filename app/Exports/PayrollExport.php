<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollExport implements FromCollection, ShouldAutoSize, WithColumnWidths, WithHeadings, WithStyles
{
    use Exportable;

    protected $tipsData;

    protected $summary;

    public function __construct($summary, $tipsData)
    {
        $this->summary = $summary;
        $this->tipsData = $tipsData;
    }

    public function collection()
    {
        $payrollData = collect();

        foreach ($this->tipsData as $employee) {
            // Split employee name by comma to get first and last name
            $nameParts = explode(',', $employee['employee_name']);
            $lastName = trim($nameParts[0] ?? '');
            $firstName = trim($nameParts[1] ?? '');

            // Get employee ID from the employee name or data
            // Note: We need to extract the employee_id from TimeEntry model
            // For now, we'll use a placeholder or extract from available data
            $employeeId = $this->getEmployeeId($employee['employee_name']);

            $payrollData->push([
                'employee_number' => $employeeId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'credit_card_tips' => number_format($employee['tip_amount'], 2),
            ]);
        }

        return $payrollData;
    }

    public function headings(): array
    {
        return [
            'Employee Number',
            'First Name',
            'Last Name',
            'Credit Card Tips',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data rows styling
        $lastRow = count($this->tipsData) + 1;
        $sheet->getStyle("A2:D{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Align currency column to the right
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Center employee number column
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Employee Number
            'B' => 20, // First Name
            'C' => 20, // Last Name
            'D' => 15, // Credit Card Tips
        ];
    }

    /**
     * Get employee ID from employee name
     * This method tries to extract employee_id from TimeEntry records
     */
    private function getEmployeeId($employeeName)
    {
        // Try to get employee_id from TimeEntry model using employee_name
        $timeEntry = \App\Models\TimeEntry::where('employee_name', $employeeName)
            ->first();

        if ($timeEntry && $timeEntry->employee_id) {
            return $timeEntry->employee_id;
        }

        // If no employee_id found, return a placeholder or generate one
        // You might want to adjust this logic based on your requirements
        return 'N/A';
    }
}
