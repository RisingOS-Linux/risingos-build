<?php
require_once __DIR__ . '/../bootstrap.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$usuario = ['id' => null, 'usuario' => ''];

if ($id) {
    $stmt = $db->prepare("SELECT id, usuario FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $encontrado = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$encontrado) {
        header('Location: usuarios.php');
        exit;
    }
    $usuario = $encontrado;
}

$title = $id ? 'Editar usuario' : 'Nuevo usuario';
require_once __DIR__ . '/../app/views/layout/header.php';
?>

<div class="page-header">
    <h1><?= $title ?></h1>
</div>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<form method="post" action="guardar_usuario.php" class="card" style="max-width: 480px;">
    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

    <div class="form-group">
        <label>Usuario</label>
        <input
            type="text"
            name="usuario"
            required
            autofocus
            value="<?= htmlspecialchars($usuario['usuario']) ?>"
        >
    </div>

    <div class="form-group">
        <label><?= $id ? 'Nueva contraseña (dejar vacío para no cambiarla)' : 'Contraseña' ?></label>
        <input
            type="password"
            name="password"
            <?= $id ? '' : 'required' ?>
            minlength="8"
        >
        <small style="color: var(--text-muted); display:block; margin-top:4px;">
            Mínimo 8 caracteres<?= $id ? ' (si la completás)' : '' ?>
        </small>
    </div>

    <div class="form-group">
        <label>Confirmar contraseña</label>
        <input
            type="password"
            name="confirmar"
            <?= $id ? '' : 'required' ?>
            minlength="8"
        >
    </div>

    <div class="page-actions">
        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>
