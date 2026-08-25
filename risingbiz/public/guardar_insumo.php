<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: insumos.php');
    exit;
}

$item = trim($_POST['item']);
$proveedor = trim($_POST['proveedor'] ?? '');
$cantidad = (int) $_POST['cantidad'];
$costo_unitario = (float) $_POST['costo_unitario'];

if ($item === '' || $cantidad <= 0 || $costo_unitario <= 0) {
    die('Datos inválidos');
}

$costo_total = $cantidad * $costo_unitario;

$stmt = $db->prepare("
    INSERT INTO insumos (item, proveedor, cantidad, costo_unitario, costo_total)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $item,
    $proveedor,
    $cantidad,
    $costo_unitario,
    $costo_total
]);

header('Location: insumos.php');
exit;
