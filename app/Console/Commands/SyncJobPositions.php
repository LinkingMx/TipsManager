<?php

namespace App\Console\Commands;

use App\Models\JobPosition;
use App\Models\TimeEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncJobPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:job-positions {--points=25 : Default points for new positions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync job positions from time entries to job_positions table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultPoints = (int) $this->option('points');

        $this->info('Syncing job positions from time entries...');

        // Get unique job titles from time entries
        $uniqueJobTitles = TimeEntry::distinct()
            ->whereNotNull('job_title')
            ->where('job_title', '!=', '')
            ->pluck('job_title')
            ->filter()
            ->sort();

        if ($uniqueJobTitles->isEmpty()) {
            $this->warn('No job titles found in time entries table.');

            return Command::SUCCESS;
        }

        $this->info("Found {$uniqueJobTitles->count()} unique job titles:");

        $created = 0;
        $skipped = 0;

        foreach ($uniqueJobTitles as $jobTitle) {
            try {
                DB::beginTransaction();

                // Check if job position already exists
                $existingPosition = JobPosition::where('name', $jobTitle)->first();

                if ($existingPosition) {
                    $this->line("  - Skipping '{$jobTitle}' (already exists)");
                    $skipped++;
                } else {
                    // Create new job position
                    JobPosition::create([
                        'name' => $jobTitle,
                        'points' => $defaultPoints,
                        'applies_for_tips' => true, // Default to true for new positions
                    ]);

                    $this->info("  + Created '{$jobTitle}' with {$defaultPoints} points");
                    $created++;
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("  ! Error creating '{$jobTitle}': ".$e->getMessage());
            }
        }

        $this->newLine();
        $this->info('Sync completed!');
        $this->info("Created: {$created} new job positions");
        $this->info("Skipped: {$skipped} existing job positions");

        // Show summary of all job positions
        $this->newLine();
        $this->info('Current job positions in database:');

        $allPositions = JobPosition::orderBy('name')->get();
        foreach ($allPositions as $position) {
            $this->line("  - {$position->name} ({$position->points} points)");
        }

        return Command::SUCCESS;
    }
}
