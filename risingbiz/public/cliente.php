<?php
require_once __DIR__ . '/../bootstrap.php';

$id = $_GET['id'] ?? null;

/* Modo edición o creación */
if ($id) {
    $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        die('Cliente no encontrado');
    }

    $title = 'Editar cliente';
} else {
    $cliente = [
        'nombre' => '',
        'telefono' => '',
        'email' => '',
        'estado' => 'Nuevo'
    ];

    $title = 'Nuevo cliente';
}

require_once __DIR__ . '/../app/views/layout/header.php';
?>

<h1><?= $title ?></h1>

<form method="post" action="guardar_cliente.php">
    <?php if ($id): ?>
        <input type="hidden" name="id" value="<?= $id ?>">
    <?php endif; ?>

    <label>Nombre y apellido</label><br>
    <input type="text" name="nombre" required
           value="<?= htmlspecialchars($cliente['nombre']) ?>"><br><br>

    <label>WhatsApp</label><br>
    <input type="text" name="telefono"
           value="<?= htmlspecialchars($cliente['telefono']) ?>"><br><br>

    <label>Email</label><br>
    <input type="email" name="email"
           value="<?= htmlspecialchars($cliente['email']) ?>"><br><br>

    <label>Estado</label><br>
    <select name="estado">
        <?php
        $estados = ['Nuevo','Contactado','Recontacto 1','Recontacto 2','Venta ganada','Irrelevante'];
        foreach ($estados as $e):
        ?>
            <option value="<?= $e ?>" <?= $cliente['estado'] === $e ? 'selected' : '' ?>>
                <?= $e ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Guardar</button>
    <a href="clientes.php">Cancelar</a>
</form>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>
