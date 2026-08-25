<?php
require_once __DIR__ . '/../bootstrap.php';

$title = 'Productos';
require_once __DIR__ . '/../app/views/layout/header.php';

$productos = $db
    ->query("SELECT * FROM productos ORDER BY creado_en DESC")
    ->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1>Productos</h1>

    <div class="page-actions">
        <a href="exportar_productos.php" class="btn btn-secondary">
            📤 Exportar
        </a>
    </div>
</div>

<div class="card" style="max-width: 520px; margin-bottom: 32px;">
    <h2 style="margin-top: 0;">➕ Nuevo producto</h2>

    <form method="post" action="guardar_producto.php" class="form-grid">
        <input
            type="text"
            name="nombre"
            placeholder="Nombre del producto"
            required
        >

        <input
            type="number"
            step="0.01"
            name="costo"
            placeholder="Costo"
            required
        >

        <input
            type="number"
            step="0.01"
            name="precio"
            placeholder="Precio de venta"
            required
        >

        <input
            type="number"
            name="stock"
            placeholder="Stock inicial"
            required
        >

        <button type="submit" class="btn btn-primary">
            Guardar producto
        </button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Costo</th>
            <th>Precio</th>
            <th>Stock</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
            <td class="cliente-nombre">
                <?= htmlspecialchars($p['nombre']) ?>
            </td>

            <td>
                $<?= number_format($p['costo'], 2) ?>
            </td>

            <td>
                $<?= number_format($p['precio'], 2) ?>
            </td>

            <td>
                <?= (int)$p['stock'] ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>
