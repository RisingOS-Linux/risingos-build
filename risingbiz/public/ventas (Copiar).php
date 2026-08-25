<?php
require_once __DIR__ . '/../bootstrap.php';

$productos = $db->query("SELECT * FROM productos WHERE stock > 0")->fetchAll(PDO::FETCH_ASSOC);
$clientes = $db->query("SELECT * FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$ventas = $db->query("
    SELECT m.*, p.nombre AS producto, c.nombre AS cliente, p.costo, p.precio
    FROM movimientos m
    JOIN productos p ON p.id = m.producto_id
    LEFT JOIN clientes c ON c.id = m.cliente_id
    WHERE m.tipo = 'venta'
    ORDER BY m.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas | RisingBiz</title>
</head>
<body>

<h1>Registrar venta</h1>

<form method="post" action="guardar_venta.php">
    <select name="producto_id" required>
        <?php foreach ($productos as $p): ?>
            <option value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['nombre']) ?> (stock <?= $p['stock'] ?>)
            </option>
        <?php endforeach; ?>
    </select><br>
    <select name="cliente_id">
    	<option value="">-- Cliente ocasional --</option>
    	<?php foreach ($clientes as $c): ?>
        	<option value="<?= $c['id'] ?>">
            		<?= htmlspecialchars($c['nombre']) ?>
        	</option>
    	<?php endforeach; ?>
     </select><br>
    <input type="number" name="cantidad" min="1" required>
    <button type="submit">Vender</button>
</form>

<h2>Ventas registradas</h2>
<table border="1" cellpadding="5">
<tr>
    <th>Producto</th>
    <th>Cliente</th>	
    <th>Cantidad</th>
    <th>Total venta</th>
    <th>Ganancia</th>
</tr>
<?php foreach ($ventas as $v): 
    $ganancia = ($v['precio'] - $v['costo']) * $v['cantidad'];
?>
<tr>
    <td><?= htmlspecialchars($v['producto']) ?></td>
    <td><?= $v['cliente'] ?? 'Ocasional' ?></td>
    <td><?= $v['cantidad'] ?></td>
    <td>$<?= number_format($v['total'], 2) ?></td>
    <td>$<?= number_format($ganancia, 2) ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
