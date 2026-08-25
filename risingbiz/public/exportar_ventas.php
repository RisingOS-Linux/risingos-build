<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=ventas_risingbiz.csv');

$output = fopen('php://output', 'w');

/* Encabezados */
fputcsv($output, [
    'Fecha',
    'Cliente',
    'Producto',
    'Cantidad',
    'Total venta',
    'Ganancia'
]);

$ventas = $db->query("
    SELECT 
        m.creado_en,
        COALESCE(c.nombre, 'Ocasional') AS cliente,
        p.nombre AS producto,
        m.cantidad,
        m.total,
        (p.precio - p.costo) * m.cantidad AS ganancia
    FROM movimientos m
    JOIN productos p ON p.id = m.producto_id
    LEFT JOIN clientes c ON c.id = m.cliente_id
    WHERE m.tipo = 'venta'
    ORDER BY m.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($ventas as $v) {
    fputcsv($output, $v);
}

fclose($output);
exit;
