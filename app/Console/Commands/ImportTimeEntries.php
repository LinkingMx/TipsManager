<?php

namespace App\Console\Commands;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTimeEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:time-entries {file=TimeEntriesToast.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import time entries from CSV file in storage/app/imports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->argument('file');
        $filePath = storage_path("app/imports/{$filename}");

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return Command::FAILURE;
        }

        $this->info("Importing time entries from: {$filename}");

        // Open CSV file
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            $this->error("Could not open file: {$filePath}");

            return Command::FAILURE;
        }

        // Read header row
        $headers = fgetcsv($handle);
        if (! $headers) {
            $this->error('Could not read CSV headers');
            fclose($handle);

            return Command::FAILURE;
        }

        $this->info('CSV Headers found: '.count($headers).' columns');

        $imported = 0;
        $errors = 0;

        // Process each row
        while (($row = fgetcsv($handle)) !== false) {
            try {
                DB::beginTransaction();

                $data = array_combine($headers, $row);

                $timeEntry = [
                    'location' => $data['Location'] ?? null,
                    'location_code' => $data['Location Code'] ?? null,
                    'external_id' => $data['Id'] ?? null,
                    'guid' => $data['GUID'] ?? null,

                    // Employee Information
                    'employee_id' => $data['Employee Id'] ?? null,
                    'employee_guid' => $data['Employee GUID'] ?? null,
                    'employee_external_id' => $data['Employee External Id'] ?? null,
                    'employee_name' => $data['Employee'] ?? null,

                    // Job Information
                    'job_id' => $data['Job Id'] ?? null,
                    'job_guid' => $data['Job GUID'] ?? null,
                    'job_code' => $data['Job Code'] ?? null,
                    'job_title' => $data['Job Title'] ?? null,

                    // Time Information
                    'in_date' => $this->parseDateTime($data['In Date'] ?? null),
                    'out_date' => $this->parseDateTime($data['Out Date'] ?? null),
                    'auto_clock_out' => ($data['Auto Clock-out'] ?? 'No') === 'Yes',

                    // Hours Information
                    'total_hours' => $this->parseDecimal($data['Total Hours'] ?? null),
                    'unpaid_break_time' => $this->parseDecimal($data['Unpaid Break Time'] ?? null),
                    'paid_break_time' => $this->parseDecimal($data['Paid Break Time'] ?? null),
                    'payable_hours' => $this->parseDecimal($data['Payable Hours'] ?? null),
                    'regular_hours' => $this->parseDecimal($data['Regular Hours'] ?? null),
                    'overtime_hours' => $this->parseDecimal($data['Overtime Hours'] ?? null),

                    // Tips Information
                    'cash_tips_declared' => $this->parseDecimal($data['Cash Tips Declared'] ?? null),
                    'non_cash_tips' => $this->parseDecimal($data['Non Cash Tips'] ?? null),
                    'total_gratuity' => $this->parseDecimal($data['Total Gratuity'] ?? null),
                    'total_tips' => $this->parseDecimal($data['Total Tips'] ?? null),
                    'tips_withheld' => $this->parseDecimal($data['Tips Withheld'] ?? null),

                    // Pay Information
                    'wage' => $this->parseDecimal($data['Wage'] ?? null),
                    'regular_pay' => $this->parseDecimal($data['Regular Pay'] ?? null),
                    'overtime_pay' => $this->parseDecimal($data['Overtime Pay'] ?? null),
                    'total_pay' => $this->parseDecimal($data['Total Pay'] ?? null),
                ];

                TimeEntry::create($timeEntry);

                DB::commit();
                $imported++;

                if ($imported % 10 === 0) {
                    $this->info("Imported {$imported} records...");
                }

            } catch (\Exception $e) {
                DB::rollBack();
                $errors++;
                $rowNumber = $imported + $errors;
                $this->warn("Error importing row {$rowNumber}: ".$e->getMessage());
            }
        }

        fclose($handle);

        $this->info('Import completed!');
        $this->info("Successfully imported: {$imported} records");

        if ($errors > 0) {
            $this->warn("Errors encountered: {$errors} records");
        }

        return Command::SUCCESS;
    }

    /**
     * Parse datetime string to Carbon instance
     */
    private function parseDateTime(?string $dateString): ?Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Handle formats like "6/18/25 7:01 AM"
            return Carbon::createFromFormat('n/j/y g:i A', $dateString);
        } catch (\Exception $e) {
            try {
                // Fallback to general parsing
                return Carbon::parse($dateString);
            } catch (\Exception $e2) {
                return null;
            }
        }
    }

    /**
     * Parse decimal string to float
     */
    private function parseDecimal(?string $value): ?float
    {
        if (empty($value) || $value === '') {
            return null;
        }

        // Remove any non-numeric characters except decimal point and minus sign
        $cleaned = preg_replace('/[^\d.-]/', '', $value);

        return is_numeric($cleaned) ? (float) $cleaned : null;
    }
}
