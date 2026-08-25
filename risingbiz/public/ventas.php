<?php
require_once __DIR__ . '/../bootstrap.php';

$title = 'Ventas';
require_once __DIR__ . '/../app/views/layout/header.php';

$productos = $db
    ->query("SELECT * FROM productos WHERE stock > 0 ORDER BY nombre")
    ->fetchAll(PDO::FETCH_ASSOC);

$clientes = $db
    ->query("SELECT * FROM clientes ORDER BY nombre")
    ->fetchAll(PDO::FETCH_ASSOC);

$ventas = $db->query("
    SELECT 
        m.*, 
        p.nombre AS producto, 
        c.nombre AS cliente, 
        p.costo, 
        p.precio
    FROM movimientos m
    JOIN productos p ON p.id = m.producto_id
    LEFT JOIN clientes c ON c.id = m.cliente_id
    WHERE m.tipo = 'venta'
    ORDER BY m.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1>Ventas</h1>

    <div class="page-actions">
        <a href="exportar_ventas.php" class="btn btn-secondary">
            📤 Exportar
        </a>
    </div>
</div>

<!-- Registrar venta -->
<div class="card" style="max-width: 560px; margin-bottom: 32px;">
    <h2 style="margin-top: 0;">➕ Registrar venta</h2>

    <form method="post" action="guardar_venta.php" class="form-grid">
        <select name="producto_id" required>
            <option value="">Producto</option>
            <?php foreach ($productos as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['nombre']) ?> · stock <?= $p['stock'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="cliente_id">
            <option value="">Cliente ocasional</option>
            <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id'] ?>">
                    <?= htmlspecialchars($c['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input
            type="number"
            name="cantidad"
            min="1"
            placeholder="Cantidad"
            required
        >

        <button type="submit" class="btn btn-primary">
            Registrar venta
        </button>
    </form>
</div>

<!-- Ventas registradas -->
<table class="table">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cliente</th>
            <th>Cantidad</th>
            <th>Total</th>
            <th>Ganancia</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($ventas as $v):
            $ganancia = ($v['precio'] - $v['costo']) * $v['cantidad'];
        ?>
        <tr>
            <td class="cliente-nombre">
                <?= htmlspecialchars($v['producto']) ?>
            </td>

            <td>
                <?= htmlspecialchars($v['cliente'] ?? 'Ocasional') ?>
            </td>

            <td>
                <?= (int)$v['cantidad'] ?>
            </td>

            <td>
                $<?= number_format($v['total'], 2) ?>
            </td>

            <td style="color: var(--verde);">
                $<?= number_format($ganancia, 2) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>
