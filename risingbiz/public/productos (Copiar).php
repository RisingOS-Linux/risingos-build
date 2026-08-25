<?php
require_once __DIR__ . '/../bootstrap.php';

$productos = $db->query("SELECT * FROM productos ORDER BY creado_en DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos | RisingBiz</title>
</head>
<body>

<h1>Productos</h1>

<h2>Agregar producto</h2>
<form method="post" action="guardar_producto.php">
    <input type="text" name="nombre" placeholder="Nombre" required><br>
    <input type="number" step="0.01" name="costo" placeholder="Costo" required><br>
    <input type="number" step="0.01" name="precio" placeholder="Precio" required><br>
    <input type="number" name="stock" placeholder="Stock inicial" required><br>
    <button type="submit">Guardar</button>
</form>

<h2>Listado</h2>
<table border="1" cellpadding="5">
<tr>
    <th>Nombre</th>
    <th>Costo</th>
    <th>Precio</th>
    <th>Stock</th>
</tr>
<?php foreach ($productos as $p): ?>
<tr>
    <td><?= htmlspecialchars($p['nombre']) ?></td>
    <td>$<?= number_format($p['costo'], 2) ?></td>
    <td>$<?= number_format($p['precio'], 2) ?></td>
    <td><?= $p['stock'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
