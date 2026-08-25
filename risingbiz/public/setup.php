<?php
require_once __DIR__ . '/../bootstrap.php';

// Si ya existe algún usuario, esta pantalla no tiene nada más que hacer acá.
$total_usuarios = (int) $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
if ($total_usuarios > 0) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Completá usuario y contraseña.';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
        $stmt->execute([$usuario, $hash]);

        // Login automático tras crear el primer admin
        $_SESSION['user_id'] = (int) $db->lastInsertId();
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración inicial | RisingBiz</title>
    <link rel="stylesheet" href="/assets/css/risingbiz.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-logo">
            <img src="/assets/img/risingbiz-logo.png" alt="RisingBiz">
        </div>

        <p style="text-align:center; color: var(--text-muted); margin-bottom: 20px;">
            Bienvenido — creá el usuario administrador para empezar a usar RisingBiz.
        </p>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Usuario</label>
                <input
                    type="text"
                    name="usuario"
                    required
                    autofocus
                    value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input
                    type="password"
                    name="password"
                    required
                    minlength="8"
                >
                <small style="color: var(--text-muted); display:block; margin-top:4px;">
                    Mínimo 8 caracteres
                </small>
            </div>

            <div class="form-group">
                <label>Confirmar contraseña</label>
                <input
                    type="password"
                    name="confirmar"
                    required
                    minlength="8"
                >
            </div>

            <button class="btn btn-primary btn-block">
                Crear administrador
            </button>
        </form>

    </div>
</div>

</body>
</html>
