<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=clientes_risingbiz.csv');

$output = fopen('php://output', 'w');

/* Encabezados */
fputcsv($output, [
    'ID',
    'Nombre',
    'Teléfono',
    'Email',
    'Estado',
    'Días en estado',
    'Fecha de creación'
]);

$clientes = $db->query("
    SELECT *,
    CAST(julianday('now') - julianday(fecha_estado) AS INTEGER) AS dias_estado
    FROM clientes
    ORDER BY creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($clientes as $c) {
    fputcsv($output, [
        $c['id'],
        $c['nombre'],
        $c['telefono'],
        $c['email'],
        $c['estado'],
        $c['dias_estado'],
        $c['creado_en']
    ]);
}

fclose($output);
exit;
