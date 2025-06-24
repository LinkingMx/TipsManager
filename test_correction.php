<?php

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DailyTip;
use App\Models\JobPosition;
use App\Models\TimeEntry;
use Carbon\Carbon;

echo "=== VERIFICACIÓN DE CORRECCIÓN DATE RANGE TIPS POOL ===\n\n";

// Simular la lógica corregida del Date Range Tips Pool
$startDate = Carbon::parse('2025-06-19');
$endDate = Carbon::parse('2025-06-22');

echo 'Período: '.$startDate->format('M j, Y').' - '.$endDate->format('M j, Y')."\n\n";

$totalPoints = 0;
$totalTipsAmount = 0;
$allEmployeeData = [];
$dailyBreakdown = [];

// Procesar cada día individualmente (como Daily Tips Pool) luego agregar
$current = $startDate->copy();

while ($current->lte($endDate)) {
    $dateString = $current->format('Y-m-d');

    echo "Procesando fecha: $dateString\n";

    // Obtener time entries para esta fecha específica
    $timeEntries = TimeEntry::whereDate('in_date', $current)
        ->whereNotNull('job_title')
        ->get();

    echo '  - Time entries: '.$timeEntries->count()."\n";

    // Agrupar time entries por empleado y trabajo para esta fecha
    $groupedEntries = [];

    foreach ($timeEntries as $entry) {
        // Verificar si job position aplica para tips
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

    // Calcular puntos para este día (misma lógica que Daily Tips Pool)
    $dayPoints = 0;
    $dayEmployees = [];

    foreach ($groupedEntries as $groupedEntry) {
        $hoursWorked = $groupedEntry['payable_hours'];
        $jobPosition = $groupedEntry['job_position'];

        // Determinar si califica para puntos completos (5+ horas)
        $qualifiesForFullPoints = $hoursWorked >= 5.0;

        // Calcular puntos usando regla de 3
        $jobPositionPoints = $jobPosition->points;
        $calculatedPoints = $qualifiesForFullPoints
            ? $jobPositionPoints
            : ($hoursWorked / 5.0) * $jobPositionPoints;

        $dayPoints += $calculatedPoints;

        $dayEmployees[] = [
            'employee_name' => $groupedEntry['employee_name'],
            'job_title' => $groupedEntry['job_title'],
            'hours_worked' => $hoursWorked,
            'calculated_points' => $calculatedPoints,
        ];

        // Acumular datos para cada empleado a través de todos los días
        $empKey = $groupedEntry['employee_name'].'|'.$groupedEntry['job_title'];

        if (! isset($allEmployeeData[$empKey])) {
            $allEmployeeData[$empKey] = [
                'employee_name' => $groupedEntry['employee_name'],
                'job_title' => $groupedEntry['job_title'],
                'total_hours' => 0,
                'total_points' => 0,
                'days_worked' => 0,
                'job_position_points' => $jobPositionPoints,
            ];
        }

        $allEmployeeData[$empKey]['total_hours'] += $hoursWorked;
        $allEmployeeData[$empKey]['total_points'] += $calculatedPoints;
        $allEmployeeData[$empKey]['days_worked']++;
    }

    $totalPoints += $dayPoints;

    // Obtener cantidad de tips diarios para esta fecha
    $dailyTip = DailyTip::whereDate('date', $current)->first();
    $dayTipsAmount = $dailyTip ? $dailyTip->amount : 0;
    $totalTipsAmount += $dayTipsAmount;

    echo '  - Empleados del día: '.count($dayEmployees)."\n";
    echo '  - Puntos del día: '.number_format($dayPoints, 2)."\n";
    echo '  - Tips del día: $'.number_format($dayTipsAmount, 2)."\n\n";

    // Agregar al desglose diario
    $dailyBreakdown[$dateString] = [
        'date' => $dateString,
        'total_employees' => count($dayEmployees),
        'total_points' => round($dayPoints, 2),
        'total_tips' => $dayTipsAmount,
        'employees' => $dayEmployees,
    ];

    $current->addDay();
}

// Calcular tip por punto para todo el período
$tipPerPoint = $totalPoints > 0 ? $totalTipsAmount / $totalPoints : 0;

echo "=== RESULTADOS CORREGIDOS ===\n";
echo 'Total puntos: '.number_format($totalPoints, 2)."\n";
echo 'Total tips: $'.number_format($totalTipsAmount, 2)."\n";
echo 'Tip por punto: $'.number_format($tipPerPoint, 2)."\n";
echo 'Empleados únicos: '.count($allEmployeeData)."\n\n";

echo "=== COMPARACIÓN CON DAILY REPORTS ===\n";
echo "Daily Reports Total Points: 722.06\n";
echo 'Corrected Range Points: '.number_format($totalPoints, 2)."\n";
echo 'Diferencia: '.number_format(722.06 - $totalPoints, 2)."\n\n";

echo "Daily Reports Total Tips: $9,846.69\n";
echo 'Corrected Range Tips: $'.number_format($totalTipsAmount, 2)."\n";
echo 'Diferencia: $'.number_format(9846.69 - $totalTipsAmount, 2)."\n\n";

if (abs(722.06 - $totalPoints) < 0.01 && abs(9846.69 - $totalTipsAmount) < 0.01) {
    echo "✅ CORRECCIÓN EXITOSA: Los totales ahora coinciden!\n";
} else {
    echo "❌ Aún hay diferencias en los cálculos\n";
}

echo "\n=== DESGLOSE DIARIO CORREGIDO ===\n";
foreach ($dailyBreakdown as $day) {
    echo 'Fecha: '.Carbon::parse($day['date'])->format('M j, Y')."\n";
    echo '  - Empleados: '.$day['total_employees']."\n";
    echo '  - Puntos: '.number_format($day['total_points'], 2)."\n";
    echo '  - Tips: $'.number_format($day['total_tips'], 2)."\n\n";
}
