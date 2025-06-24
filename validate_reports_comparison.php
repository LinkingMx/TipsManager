<?php

require_once 'vendor/autoload.php';

use App\Models\DailyTip;
use App\Models\JobPosition;
use App\Models\TimeEntry;
use Carbon\Carbon;

// Boot Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VALIDATION: Daily Tips Pool vs Date Range Tips Pool ===\n\n";

function calculateDailyTipsPool($date)
{
    $date = Carbon::parse($date);

    echo 'Calculating Daily Tips Pool for: '.$date->format('Y-m-d')."\n";

    // Get time entries for the date
    $timeEntries = TimeEntry::whereDate('in_date', $date)
        ->whereNotNull('job_title')
        ->get();

    echo 'Found '.$timeEntries->count()." time entries\n";

    $groupedEntries = [];
    $totalPoints = 0;

    foreach ($timeEntries as $entry) {
        $jobPosition = JobPosition::where('name', $entry->job_title)->first();

        if (! $jobPosition || ! $jobPosition->applies_for_tips) {
            continue;
        }

        $key = $entry->employee_name.'|'.$entry->job_title;

        if (! isset($groupedEntries[$key])) {
            $groupedEntries[$key] = [
                'employee_name' => $entry->employee_name,
                'job_title' => $entry->job_title,
                'payable_hours' => 0,
                'job_position' => $jobPosition,
            ];
        }

        $groupedEntries[$key]['payable_hours'] += $entry->payable_hours ?? 0;
    }

    $employeeData = [];

    foreach ($groupedEntries as $groupedEntry) {
        $hoursWorked = $groupedEntry['payable_hours'];
        $jobPosition = $groupedEntry['job_position'];

        $qualifiesForFullPoints = $hoursWorked >= 5.0;
        $jobPositionPoints = $jobPosition->points;
        $calculatedPoints = $qualifiesForFullPoints
            ? $jobPositionPoints
            : ($hoursWorked / 5.0) * $jobPositionPoints;

        $totalPoints += $calculatedPoints;

        $employeeData[] = [
            'employee_name' => $groupedEntry['employee_name'],
            'job_title' => $groupedEntry['job_title'],
            'hours_worked' => $hoursWorked,
            'calculated_points' => $calculatedPoints,
        ];
    }

    $dailyTip = DailyTip::whereDate('date', $date)->first();
    $totalTipsAmount = $dailyTip ? $dailyTip->amount : 0;

    echo 'Daily Logic - Total Points: '.round($totalPoints, 2)."\n";
    echo 'Daily Logic - Tips Amount: $'.$totalTipsAmount."\n";
    echo 'Daily Logic - Employees: '.count($employeeData)."\n\n";

    return [
        'total_points' => $totalPoints,
        'total_tips' => $totalTipsAmount,
        'employees' => $employeeData,
        'employee_count' => count($employeeData),
    ];
}

function calculateDateRangeTipsPool($startDate, $endDate)
{
    $startDate = Carbon::parse($startDate);
    $endDate = Carbon::parse($endDate);

    echo 'Calculating Date Range Tips Pool from: '.$startDate->format('Y-m-d').' to '.$endDate->format('Y-m-d')."\n";

    $totalPoints = 0;
    $totalTipsAmount = 0;
    $allEmployeeData = [];

    $current = $startDate->copy();

    while ($current->lte($endDate)) {
        echo 'Processing date: '.$current->format('Y-m-d')."\n";

        $timeEntries = TimeEntry::whereDate('in_date', $current)
            ->whereNotNull('job_title')
            ->get();

        echo '  Found '.$timeEntries->count()." time entries\n";

        $groupedEntries = [];

        foreach ($timeEntries as $entry) {
            $jobPosition = JobPosition::where('name', $entry->job_title)->first();

            if (! $jobPosition || ! $jobPosition->applies_for_tips) {
                continue;
            }

            $key = $entry->employee_name.'|'.$entry->job_title;

            if (! isset($groupedEntries[$key])) {
                $groupedEntries[$key] = [
                    'employee_name' => $entry->employee_name,
                    'job_title' => $entry->job_title,
                    'payable_hours' => 0,
                    'job_position' => $jobPosition,
                ];
            }

            $groupedEntries[$key]['payable_hours'] += $entry->payable_hours ?? 0;
        }

        $dayPoints = 0;

        foreach ($groupedEntries as $groupedEntry) {
            $hoursWorked = $groupedEntry['payable_hours'];
            $jobPosition = $groupedEntry['job_position'];

            $qualifiesForFullPoints = $hoursWorked >= 5.0;
            $jobPositionPoints = $jobPosition->points;
            $calculatedPoints = $qualifiesForFullPoints
                ? $jobPositionPoints
                : ($hoursWorked / 5.0) * $jobPositionPoints;

            $dayPoints += $calculatedPoints;

            $empKey = $groupedEntry['employee_name'].'|'.$groupedEntry['job_title'];

            if (! isset($allEmployeeData[$empKey])) {
                $allEmployeeData[$empKey] = [
                    'employee_name' => $groupedEntry['employee_name'],
                    'job_title' => $groupedEntry['job_title'],
                    'total_hours' => 0,
                    'total_points' => 0,
                    'days_worked' => 0,
                ];
            }

            $allEmployeeData[$empKey]['total_hours'] += $hoursWorked;
            $allEmployeeData[$empKey]['total_points'] += $calculatedPoints;
            $allEmployeeData[$empKey]['days_worked']++;
        }

        $totalPoints += $dayPoints;

        $dailyTip = DailyTip::whereDate('date', $current)->first();
        $dayTipsAmount = $dailyTip ? $dailyTip->amount : 0;
        $totalTipsAmount += $dayTipsAmount;

        echo '  Day Points: '.round($dayPoints, 2).', Day Tips: $'.$dayTipsAmount."\n";

        $current->addDay();
    }

    echo 'Range Logic - Total Points: '.round($totalPoints, 2)."\n";
    echo 'Range Logic - Tips Amount: $'.$totalTipsAmount."\n";
    echo 'Range Logic - Unique Employees: '.count($allEmployeeData)."\n\n";

    return [
        'total_points' => $totalPoints,
        'total_tips' => $totalTipsAmount,
        'employees' => $allEmployeeData,
        'employee_count' => count($allEmployeeData),
    ];
}

// Test cases
$testDates = [
    '2025-06-19',  // Single day
    '2025-06-20',  // Single day
    '2025-06-21',  // Single day
    '2025-06-22',  // Single day
];

echo "SINGLE DAY TESTS:\n";
echo "================\n\n";

foreach ($testDates as $testDate) {
    echo "--- TESTING DATE: $testDate ---\n";

    $dailyResult = calculateDailyTipsPool($testDate);
    $rangeResult = calculateDateRangeTipsPool($testDate, $testDate);

    echo "COMPARISON:\n";
    echo 'Daily Points: '.round($dailyResult['total_points'], 2).' | Range Points: '.round($rangeResult['total_points'], 2)."\n";
    echo 'Daily Tips: $'.$dailyResult['total_tips'].' | Range Tips: $'.$rangeResult['total_tips']."\n";
    echo 'Daily Employees: '.$dailyResult['employee_count'].' | Range Employees: '.$rangeResult['employee_count']."\n";

    $pointsMatch = abs($dailyResult['total_points'] - $rangeResult['total_points']) < 0.01;
    $tipsMatch = $dailyResult['total_tips'] == $rangeResult['total_tips'];
    $employeesMatch = $dailyResult['employee_count'] == $rangeResult['employee_count'];

    echo 'Points Match: '.($pointsMatch ? '✓ YES' : '✗ NO')."\n";
    echo 'Tips Match: '.($tipsMatch ? '✓ YES' : '✗ NO')."\n";
    echo 'Employees Match: '.($employeesMatch ? '✓ YES' : '✗ NO')."\n";

    if (! $pointsMatch || ! $tipsMatch || ! $employeesMatch) {
        echo "❌ MISMATCH DETECTED!\n";
    } else {
        echo "✅ PERFECT MATCH!\n";
    }

    echo "\n".str_repeat('=', 50)."\n\n";
}

// Multi-day range test
echo "MULTI-DAY RANGE TEST:\n";
echo "====================\n\n";

echo "--- TESTING RANGE: 2025-06-19 to 2025-06-22 ---\n";

$rangeResult = calculateDateRangeTipsPool('2025-06-19', '2025-06-22');

// Calculate sum of individual daily reports
$sumDailyPoints = 0;
$sumDailyTips = 0;
$allDailyEmployees = [];

foreach ($testDates as $date) {
    $dailyResult = calculateDailyTipsPool($date);
    $sumDailyPoints += $dailyResult['total_points'];
    $sumDailyTips += $dailyResult['total_tips'];

    foreach ($dailyResult['employees'] as $emp) {
        $key = $emp['employee_name'].'|'.$emp['job_title'];
        if (! isset($allDailyEmployees[$key])) {
            $allDailyEmployees[$key] = [
                'employee_name' => $emp['employee_name'],
                'job_title' => $emp['job_title'],
                'total_points' => 0,
                'total_hours' => 0,
                'days' => 0,
            ];
        }
        $allDailyEmployees[$key]['total_points'] += $emp['calculated_points'];
        $allDailyEmployees[$key]['total_hours'] += $emp['hours_worked'];
        $allDailyEmployees[$key]['days']++;
    }
}

echo "FINAL COMPARISON:\n";
echo 'Sum of Daily Points: '.round($sumDailyPoints, 2).' | Range Points: '.round($rangeResult['total_points'], 2)."\n";
echo 'Sum of Daily Tips: $'.$sumDailyTips.' | Range Tips: $'.$rangeResult['total_tips']."\n";
echo 'Daily Unique Employees: '.count($allDailyEmployees).' | Range Employees: '.$rangeResult['employee_count']."\n";

$pointsMatch = abs($sumDailyPoints - $rangeResult['total_points']) < 0.01;
$tipsMatch = $sumDailyTips == $rangeResult['total_tips'];
$employeesMatch = count($allDailyEmployees) == $rangeResult['employee_count'];

echo 'Points Match: '.($pointsMatch ? '✓ YES' : '✗ NO')."\n";
echo 'Tips Match: '.($tipsMatch ? '✓ YES' : '✗ NO')."\n";
echo 'Employees Match: '.($employeesMatch ? '✓ YES' : '✗ NO')."\n";

if (! $pointsMatch || ! $tipsMatch || ! $employeesMatch) {
    echo "❌ RANGE TOTALS MISMATCH!\n";

    if (! $pointsMatch) {
        echo 'Points difference: '.abs($sumDailyPoints - $rangeResult['total_points'])."\n";
    }
} else {
    echo "✅ RANGE TOTALS PERFECT MATCH!\n";
}

echo "\n=== VALIDATION COMPLETE ===\n";
