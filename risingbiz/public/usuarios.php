<?php
require_once __DIR__ . '/../bootstrap.php';
$title = 'Usuarios';
require_once __DIR__ . '/../app/views/layout/header.php';

$usuarios = $db->query("SELECT id, usuario FROM usuarios ORDER BY usuario ASC")
                ->fetchAll(PDO::FETCH_ASSOC);
$total = count($usuarios);
?>

<div class="page-header">
    <h1>Usuarios</h1>

    <div class="page-actions">
        <a href="usuario.php" class="btn btn-primary">
            ➕ Nuevo usuario
        </a>
    </div>
</div>

<?php if (!empty($_GET['ok'])): ?>
    <div class="alert alert-success">
        <?= $_GET['ok'] === 'eliminado' ? 'Usuario eliminado.' : 'Usuario guardado.' ?>
    </div>
<?php endif; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-error">
        <?= $_GET['error'] === 'ultimo' ? 'No podés eliminar el único usuario del sistema.' : htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<table class="table">
<thead>
<tr>
    <th>Usuario</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>
<?php foreach ($usuarios as $u): ?>
<tr>
    <td class="cliente-nombre">
        <?= htmlspecialchars($u['usuario']) ?>
        <?php if ((int)$u['id'] === (int)($_SESSION['user_id'] ?? 0)): ?>
            <span class="badge" style="background: var(--cian);">vos</span>
        <?php endif; ?>
    </td>
    <td>
        <div class="acciones">
            <a href="usuario.php?id=<?= $u['id'] ?>" class="btn btn-secondary btn-icon">
                ✏ Editar
            </a>

            <?php if ($total > 1): ?>
            <form
                method="post"
                action="eliminar_usuario.php"
                style="display:inline"
                onsubmit="return confirm('¿Eliminar este usuario? No se puede deshacer.');"
            >
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" class="btn btn-danger btn-icon">
                    🗑 Eliminar
                </button>
            </form>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php require_once __DIR__ . '/../app/views/layout/footer.php'; ?>
