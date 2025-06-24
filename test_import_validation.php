<?php

require_once 'vendor/autoload.php';

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Filament\Imports\TimeEntryImporter;

echo "=== VALIDACIÓN DE TIMEENTRYIMPORTER ===\n\n";

// 1. Verificar las columnas del importer
echo "=== COLUMNAS DEL IMPORTER ===\n";
$columns = TimeEntryImporter::getColumns();
$employeeIdColumn = null;
$employeeNameColumn = null;

foreach ($columns as $column) {
    $columnName = $column->getName();
    if ($columnName === 'employee_id') {
        $employeeIdColumn = $column;
        echo "✓ Columna 'employee_id' encontrada\n";

        // Verificar guess patterns para Employee Id
        $guessPatterns = $column->getGuess();
        echo '  - Patrones de detección: '.implode(', ', $guessPatterns)."\n";

        if (in_array('employee_id', $guessPatterns) || in_array('employee id', $guessPatterns)) {
            echo "  ✓ Incluye patrones para 'Employee Id'\n";
        } else {
            echo "  ⚠ No incluye patrones exactos para 'Employee Id'\n";
        }
    }

    if ($columnName === 'employee_name') {
        $employeeNameColumn = $column;
        echo "✓ Columna 'employee_name' encontrada\n";

        // Verificar guess patterns para Employee
        $guessPatterns = $column->getGuess();
        echo '  - Patrones de detección: '.implode(', ', $guessPatterns)."\n";

        if (in_array('employee', $guessPatterns)) {
            echo "  ✓ Incluye patrón para 'Employee'\n";
        } else {
            echo "  ⚠ No incluye patrón para 'Employee'\n";
        }
    }
}

// 2. Probar el casting de employee_id con valores grandes
echo "\n=== PRUEBA DE EMPLOYEE_ID CASTING ===\n";
if ($employeeIdColumn) {
    $testValues = [
        '1200000004664296653',
        1200000004664296653,
        '1200000004664290000',
        1200000004664290000,
    ];

    foreach ($testValues as $testValue) {
        try {
            // Simular el proceso de casting
            $castFunction = $employeeIdColumn->getCastStateUsing();
            if ($castFunction) {
                $result = $castFunction($testValue);
                echo "✓ Valor '$testValue' (".gettype($testValue).") -> '$result' (".gettype($result).")\n";
            } else {
                echo "⚠ No hay función de casting definida\n";
            }
        } catch (Exception $e) {
            echo "✗ Error al procesar '$testValue': ".$e->getMessage()."\n";
        }
    }
}

// 3. Verificar que el modelo de destino es correcto
echo "\n=== VALIDACIÓN DEL MODELO ===\n";
$model = TimeEntryImporter::getModel();
echo "✓ Modelo destino: $model\n";

// 4. Leer algunas líneas del CSV para verificar el formato
echo "\n=== MUESTRA DEL CSV ===\n";
$csvPath = 'storage/app/imports/test_upload_tentries.csv';
if (file_exists($csvPath)) {
    $handle = fopen($csvPath, 'r');
    $headers = fgetcsv($handle);
    $firstRow = fgetcsv($handle);
    fclose($handle);

    echo 'Headers CSV: '.implode(', ', $headers)."\n";
    echo "Primera fila de datos:\n";

    $employeeIdIndex = array_search('Employee Id', $headers);
    $employeeIndex = array_search('Employee', $headers);

    if ($employeeIdIndex !== false) {
        echo '  - Employee Id: '.$firstRow[$employeeIdIndex]."\n";
    }

    if ($employeeIndex !== false) {
        echo '  - Employee: '.$firstRow[$employeeIndex]."\n";
    }
} else {
    echo "⚠ Archivo CSV no encontrado en $csvPath\n";
}

echo "\n=== VALIDACIÓN COMPLETADA ===\n";
