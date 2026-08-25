<?php
require_once __DIR__ . '/../bootstrap.php';

$ventas_por_dia = $db->query("
    SELECT DATE(creado_en) as fecha, SUM(total) as total
    FROM movimientos
    WHERE tipo = 'venta'
      AND creado_en >= date('now', '-7 days')
    GROUP BY DATE(creado_en)
    ORDER BY fecha
")->fetchAll(PDO::FETCH_ASSOC);

$clientes_por_estado = $db->query("
    SELECT estado, COUNT(*) as total
    FROM clientes
    GROUP BY estado
")->fetchAll(PDO::FETCH_ASSOC);

$top_productos = $db->query("
    SELECT p.nombre, SUM(m.cantidad) as unidades
    FROM movimientos m
    JOIN productos p ON p.id = m.producto_id
    WHERE m.tipo = 'venta'
    GROUP BY p.id
    ORDER BY unidades DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

/* Total insumos */
$insumos = $db->query("
    SELECT SUM(costo_total) AS total_insumos
    FROM insumos
")->fetch(PDO::FETCH_ASSOC);

/* Totales de ventas/ganancia — DEBE calcularse antes de usar $resumen */
$resumen = $db->query("
    SELECT 
        SUM(m.total) AS ventas_totales,
        SUM((p.precio - p.costo) * m.cantidad) AS ganancia_total
    FROM movimientos m
    JOIN productos p ON p.id = m.producto_id
    WHERE m.tipo = 'venta'
")->fetch(PDO::FETCH_ASSOC);

$ventas_totales = $resumen['ventas_totales'] ?? 0;
$insumos_totales = $insumos['total_insumos'] ?? 0;
$balance = $ventas_totales - $insumos_totales;

$title = 'Dashboard';

/* Productos con stock */
$productos_con_stock = $db
    ->query("SELECT COUNT(*) FROM productos WHERE stock > 0")
    ->fetchColumn();

/* Clientes */
$total_clientes = $db
    ->query("SELECT COUNT(*) FROM clientes")
    ->fetchColumn();

require_once __DIR__ . '/../app/views/layout/header.php';
?>

<section class="dashboard-grid">

  <div class="card">
    <h3>Ventas totales</h3>
    <div class="value">
      $<?= number_format($resumen['ventas_totales'] ?? 0, 2) ?>
    </div>
  </div>

  <div class="card">
    <h3>Ganancia total</h3>
    <div class="value" style="color: var(--verde);">
      $<?= number_format($resumen['ganancia_total'] ?? 0, 2) ?>
    </div>
  </div>

  <div class="card">
    <h3>Productos con stock</h3>
    <div class="value">
      <?= $productos_con_stock ?>
    </div>
  </div>

  <div class="card">
    <h3>Clientes registrados</h3>
    <div class="value">
      <?= $total_clientes ?>
    </div>
  </div>
  
  <div class="card">
    <h3>Insumos</h3>
    <div class="value" style="color: var(--rojo);">
      $<?= number_format($insumos_totales, 2) ?>
    </div>
  </div>

  <div class="card">
    <h3>Balance</h3>
    <div class="value" style="color: <?= $balance >= 0 ? 'var(--verde)' : 'var(--rojo)' ?>;">
      $<?= number_format($balance, 2) ?>
    </div>
  </div>
</section>

<section class="dashboard-grid-2">

  <div class="card">
    <h3>Ventas últimos 7 días</h3>
    <canvas id="ventasChart"></canvas>
  </div>

  <div class="card">
    <h3>Clientes por estado</h3>
        <div class="chart-small">
            <canvas id="clientesChart"></canvas>
  </div>

</section>

<section class="dashboard-grid-1">
  <div class="card">
    <h3>Top productos vendidos</h3>
    <table class="table">
      <tr>
        <th>Producto</th>
        <th>Unidades</th>
      </tr>
      <?php foreach ($top_productos as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td><?= $p['unidades'] ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ventasData = {
  labels: <?= json_encode(array_column($ventas_por_dia, 'fecha')) ?>,
  datasets: [{
    label: 'Ventas',
    data: <?= json_encode(array_column($ventas_por_dia, 'total')) ?>,
    borderColor: '#21B6E6',
    backgroundColor: 'rgba(33,182,230,0.2)',
    tension: 0.3
  }]
};

new Chart(document.getElementById('ventasChart'), {
  type: 'line',
  data: ventasData,
  options: {
    responsive: true,
    plugins: { legend: { display: false } }
  }
});

const data = {
  labels: ['Ventas', 'Insumos'],
  datasets: [{
    data: [<?= $ventas_totales ?>, <?= $insumos_totales ?>]
  }]
};

const clientesData = {
  labels: <?= json_encode(array_column($clientes_por_estado, 'estado')) ?>,
  datasets: [{
    data: <?= json_encode(array_column($clientes_por_estado, 'total')) ?>,
    backgroundColor: [
      '#21B6E6', '#F2E94E', '#E6429A', '#4CAF50', '#E53935'
    ]
  }]
};

new Chart(document.getElementById('clientesChart'), {
  type: 'doughnut',
  data: clientesData,
  options: {
    responsive: true
  }
});
</script>

<?php
require_once __DIR__ . '/../app/views/layout/footer.php';
