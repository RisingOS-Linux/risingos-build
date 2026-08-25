<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=insumos.csv');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'Fecha',
    'Item',
    'Proveedor',
    'Cantidad',
    'Costo unitario',
    'Total'
]);

$rows = $db->query("
    SELECT i.*, p.nombre AS proveedor
    FROM insumos i
    LEFT JOIN proveedores p ON p.id = i.proveedor_id
    ORDER BY i.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    fputcsv($output, [
        $r['creado_en'],
        $r['item'],
        $r['proveedor'] ?? 'Sin proveedor',
        $r['cantidad'],
        $r['costo'],
        $r['cantidad'] * $r['costo']
    ]);
}

fclose($output);
exit;
