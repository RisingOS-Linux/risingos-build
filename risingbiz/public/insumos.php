<?php
require_once __DIR__ . '/../bootstrap.php';

$title = 'Insumos';
require_once __DIR__ . '/../app/views/layout/header.php';

$insumos = $db->query("
    SELECT *
    FROM insumos
    ORDER BY fecha_compra DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1>Insumos</h1>

    <div class="page-actions">
        <a href="exportar_insumos.php" class="btn btn-secondary">
            📤 Exportar
        </a>
    </div>
</div>

<div class="card">
    <h3>Registrar compra de insumo</h3>

    <form method="post" action="guardar_insumo.php" class="form-grid">
        <input type="text" name="item" placeholder="Item / Insumo" required>
        <input type="text" name="proveedor" placeholder="Proveedor">
        <input type="number" name="cantidad" placeholder="Cantidad" min="1" required>
        <input type="number" step="0.01" name="costo_unitario" placeholder="Costo unitario" required>

        <button class="btn btn-primary" type="submit">
            💾 Guardar compra
        </button>
    </form>
</div>

<div class="card">
    <h3>Historial de insumos</h3>

    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Item</th>
                <th>Proveedor</th>
                <th>Cantidad</th>
                <th>Costo unitario</th>
                <th>Costo total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($insumos as $i): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($i['fecha_compra'])) ?></td>
                <td><?= htmlspecialchars($i['item']) ?></td>
                <td><?= htmlspecialchars($i['proveedor'] ?? '-') ?></td>
                <td><?= $i['cantidad'] ?></td>
                <td>$<?= number_format($i['costo_unitario'], 2) ?></td>
                <td><b>$<?= number_format($i['costo_total'], 2) ?></b></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>
