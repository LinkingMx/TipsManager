<?php

// Script simple para validar el mapping del CSV
$csvPath = '/Users/armando_reyes/Herd/TipsManager/storage/app/imports/test_upload_tentries.csv';

echo "=== VALIDACIÓN COMPLETA DEL IMPORTER ===\n\n";

// 1. Verificar que el archivo CSV existe
if (! file_exists($csvPath)) {
    echo "❌ ERROR: Archivo CSV no encontrado en: $csvPath\n";
    exit(1);
}

echo "✅ Archivo CSV encontrado\n";

// 2. Leer headers del CSV
$handle = fopen($csvPath, 'r');
$headers = fgetcsv($handle);
$firstRow = fgetcsv($handle);
$secondRow = fgetcsv($handle);
fclose($handle);

echo "\n=== HEADERS DEL CSV ===\n";
foreach ($headers as $index => $header) {
    echo "[$index] $header\n";
}

// 3. Buscar columnas específicas
echo "\n=== MAPEO DE COLUMNAS CRÍTICAS ===\n";

$employeeIdIndex = array_search('Employee Id', $headers);
$employeeIndex = array_search('Employee', $headers);
$jobTitleIndex = array_search('Job Title', $headers);
$inDateIndex = array_search('In Date', $headers);
$outDateIndex = array_search('Out Date', $headers);

if ($employeeIdIndex !== false) {
    echo "✅ 'Employee Id' encontrado en posición $employeeIdIndex\n";
    echo '   Valor ejemplo: '.$firstRow[$employeeIdIndex]."\n";
    echo '   Tipo: '.gettype($firstRow[$employeeIdIndex])."\n";
    echo '   Longitud: '.strlen($firstRow[$employeeIdIndex])." caracteres\n";
} else {
    echo "❌ 'Employee Id' NO encontrado\n";
}

if ($employeeIndex !== false) {
    echo "✅ 'Employee' encontrado en posición $employeeIndex\n";
    echo '   Valor ejemplo: '.$firstRow[$employeeIndex]."\n";
} else {
    echo "❌ 'Employee' NO encontrado\n";
}

if ($jobTitleIndex !== false) {
    echo "✅ 'Job Title' encontrado en posición $jobTitleIndex\n";
    echo '   Valor ejemplo: '.$firstRow[$jobTitleIndex]."\n";
} else {
    echo "❌ 'Job Title' NO encontrado\n";
}

if ($inDateIndex !== false) {
    echo "✅ 'In Date' encontrado en posición $inDateIndex\n";
    echo '   Valor ejemplo: '.$firstRow[$inDateIndex]."\n";
} else {
    echo "❌ 'In Date' NO encontrado\n";
}

// 4. Verificar algunos valores de Employee Id para problemas de precisión
echo "\n=== VERIFICACIÓN DE EMPLOYEE IDs ===\n";
$handle = fopen($csvPath, 'r');
fgetcsv($handle); // Skip headers

$count = 0;
while (($row = fgetcsv($handle)) !== false && $count < 5) {
    if ($employeeIdIndex !== false) {
        $employeeId = $row[$employeeIdIndex];
        $employeeName = $employeeIndex !== false ? $row[$employeeIndex] : 'N/A';

        echo 'Registro '.($count + 1).":\n";
        echo "  - Employee ID: '$employeeId' (tipo: ".gettype($employeeId).', longitud: '.strlen($employeeId).")\n";
        echo "  - Employee: '$employeeName'\n";
        echo "  - Como string: '".(string) $employeeId."'\n";
        echo "  - Conversión manual: '".sprintf('%.0f', (float) $employeeId)."'\n";
        echo "\n";
    }
    $count++;
}
fclose($handle);

echo "=== VALIDACIÓN COMPLETADA ===\n";
