<?php
require_once __DIR__ . '/../bootstrap.php';
$title = 'Clientes';
require_once __DIR__ . '/../app/views/layout/header.php';

$estado_colores = [
    'Nuevo' => 'var(--cian)',
    'Contactado' => 'var(--amarillo)',
    'Recontacto 1' => 'var(--magenta)',
    'Recontacto 2' => 'var(--magenta)',
    'Venta ganada' => 'var(--verde)',
    'Irrelevante' => 'var(--rojo)'
];

$clientes = $db->query("
    SELECT *,
    CAST(julianday('now') - julianday(fecha_estado) AS INTEGER) AS dias_estado
    FROM clientes
    ORDER BY creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h1>Clientes</h1>

    <a href="cliente.php" class="btn btn-primary">
        ➕ Nuevo cliente
    </a>
</div>

<div class="clientes-filtros">
    <input
        type="text"
        id="busquedaCliente"
        placeholder="🔍 Buscar cliente..."
        class="input"
    >

    <select id="filtroEstado" class="input">
        <option value="">Todos los estados</option>
        <?php foreach ($estado_colores as $estado => $_): ?>
            <option value="<?= htmlspecialchars($estado) ?>">
                <?= htmlspecialchars($estado) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<form method="post" action="clientes_estado_lote.php">

<table class="table">
<thead>
<tr>
    <th><input type="checkbox" id="checkAll"></th>
    <th>Nombre</th>
    <th>Contacto</th>
    <th>Estado</th>
    <th>Días</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>
<?php foreach ($clientes as $c): ?>
<tr>
    <td>
        <input type="checkbox" name="ids[]" value="<?= $c['id'] ?>">
    </td>

    <td class="cliente-nombre">
		<?= htmlspecialchars($c['nombre']) ?>
	</td>

    <td>
        <div class="contacto-grid">
            <?php if ($c['telefono']): ?>
                <div class="contacto-item">
                    📞 <span><?= htmlspecialchars($c['telefono']) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($c['email']): ?>
                <div class="contacto-item">
                    ✉️ <span><?= htmlspecialchars($c['email']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </td>

    <td>
        <span class="badge" style="background: <?= $estado_colores[$c['estado']] ?? '#666' ?>">
            <?= htmlspecialchars($c['estado']) ?>
        </span>
    </td>

    <td><?= (int)$c['dias_estado'] ?> días</td>

    <td>
        <div class="acciones">
            <a href="cliente.php?id=<?= $c['id'] ?>" class="btn btn-secondary btn-icon">
                ✏ Editar
            </a>

            <?php if ($c['telefono']): ?>
            <a
              href="https://web.whatsapp.com/send?phone=<?= preg_replace('/\D/', '', $c['telefono']) ?>"
              target="_blank"
              class="btn btn-success btn-icon"
            >
              💬 WhatsApp
            </a>
            <?php endif; ?>
        </div>
    </td>
<tr
  data-estado="<?= htmlspecialchars($c['estado']) ?>"
  data-texto="<?= strtolower(
      $c['nombre'] . ' ' .
      $c['telefono'] . ' ' .
      $c['email']
  ) ?>"
>
<?php endforeach; ?>
</tbody>
</table>

<div class="bulk-actions">
    <select name="estado" required>
        <option value="">Cambiar estado a...</option>
        <?php foreach ($estado_colores as $estado => $_): ?>
            <option value="<?= $estado ?>"><?= $estado ?></option>
        <?php endforeach; ?>
    </select>

    <button class="btn btn-secondary" type="submit">
        Aplicar a seleccionados
    </button>
</div>

</form>

<script>
// Check all (ya lo tenías)
document.getElementById('checkAll').addEventListener('change', e => {
    document.querySelectorAll('input[name="ids[]"]').forEach(c => {
        c.checked = e.target.checked;
    });
});

// 🔍 Filtro y búsqueda
const busqueda = document.getElementById('busquedaCliente');
const filtroEstado = document.getElementById('filtroEstado');
const filas = document.querySelectorAll('tbody tr');

function filtrarClientes() {
    const texto = busqueda.value.toLowerCase();
    const estado = filtroEstado.value;

    filas.forEach(fila => {
        const textoFila = fila.dataset.texto;
        const estadoFila = fila.dataset.estado;

        const coincideTexto = textoFila.includes(texto);
        const coincideEstado = !estado || estadoFila === estado;

        fila.style.display =
            coincideTexto && coincideEstado ? '' : 'none';
    });
}

busqueda.addEventListener('input', filtrarClientes);
filtroEstado.addEventListener('change', filtrarClientes);

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>
</script>
