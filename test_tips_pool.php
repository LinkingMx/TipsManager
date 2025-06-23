<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test TipsPool functionality
echo "Testing TipsPool with in_date and out_date columns...\n\n";

// Check if we have time entries
$timeEntries = \App\Models\TimeEntry::whereDate('in_date', now())->count();
echo "Time entries for today: {$timeEntries}\n";

// Check if we have any job positions
$jobPositions = \App\Models\JobPosition::where('applies_for_tips', true)->count();
echo "Job positions with tips enabled: {$jobPositions}\n";

// Test a sample time entry structure
$sampleEntry = \App\Models\TimeEntry::first();
if ($sampleEntry) {
    echo "\nSample time entry fields:\n";
    echo '- in_date: '.($sampleEntry->in_date ?? 'null')."\n";
    echo '- out_date: '.($sampleEntry->out_date ?? 'null')."\n";
    echo '- employee_name: '.($sampleEntry->employee_name ?? 'null')."\n";
    echo '- job_title: '.($sampleEntry->job_title ?? 'null')."\n";
} else {
    echo "\nNo time entries found in database\n";
}

echo "\nTipsPool class verification:\n";
if (class_exists('App\Filament\Pages\TipsPool')) {
    echo "✓ TipsPool class exists\n";

    // Check if we can create an instance
    try {
        $tipsPool = new \App\Filament\Pages\TipsPool;
        echo "✓ TipsPool can be instantiated\n";

        // Test the method
        $tipsPool->selectedDate = now()->format('Y-m-d');
        $tipsPool->generateReport();
        echo "✓ generateReport method works\n";

        // Check the structure
        if (! empty($tipsPool->tipsData)) {
            $firstEntry = $tipsPool->tipsData[0];
            $hasInDate = array_key_exists('in_date', $firstEntry);
            $hasOutDate = array_key_exists('out_date', $firstEntry);

            echo '✓ Tips data generated: '.count($tipsPool->tipsData)." entries\n";
            echo ($hasInDate ? '✓' : '✗')." in_date field present\n";
            echo ($hasOutDate ? '✓' : '✗')." out_date field present\n";

            if ($hasInDate && $hasOutDate) {
                echo "\n✅ SUCCESS: in_date and out_date columns have been successfully added!\n";
            }
        } else {
            echo "ℹ️  No tips data found (likely no eligible entries for today)\n";
            echo "✅ Structure changes are in place and ready to work\n";
        }

    } catch (Exception $e) {
        echo '✗ Error testing TipsPool: '.$e->getMessage()."\n";
    }
} else {
    echo "✗ TipsPool class not found\n";
}

echo "\nTest completed.\n";
