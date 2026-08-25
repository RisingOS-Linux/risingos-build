<?php
require_once __DIR__ . '/../bootstrap.php';

$db->beginTransaction();

$producto = $db->prepare("SELECT * FROM productos WHERE id = ?");
$producto->execute([$_POST['producto_id']]);
$p = $producto->fetch(PDO::FETCH_ASSOC);

$cantidad = (int) $_POST['cantidad'];

if ($p && $p['stock'] >= $cantidad) {

    $total = $p['precio'] * $cantidad;

    $db->prepare("
    	INSERT INTO movimientos (tipo, producto_id, cantidad, total, cliente_id)
    	VALUES ('venta', ?, ?, ?, ?)
    ")->execute([
    	$p['id'],
    	$cantidad,
    	$total,
    	$_POST['cliente_id'] ?: null
    ]);


    $db->prepare("
        UPDATE productos SET stock = stock - ?
        WHERE id = ?
    ")->execute([$cantidad, $p['id']]);

    $db->commit();
}

header('Location: ventas.php');
exit;
