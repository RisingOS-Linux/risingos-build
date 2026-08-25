<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=productos_risingbiz.csv');

$output = fopen('php://output', 'w');

/* Encabezados */
fputcsv($output, [
    'ID',
    'Producto',
    'Costo unitario',
    'Precio venta',
    'Stock actual',
    'Fecha de creación'
]);

$productos = $db->query("
    SELECT *
    FROM productos
    ORDER BY creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos as $p) {
    fputcsv($output, [
        $p['id'],
        $p['nombre'],
        $p['costo'],
        $p['precio'],
        $p['stock'],
        $p['creado_en']
    ]);
}

fclose($output);
exit;
