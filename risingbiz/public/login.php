<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$_POST['usuario']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    }

    $error = "Credenciales inválidas";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | RisingBiz</title>
    <link rel="stylesheet" href="/assets/css/risingbiz.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-logo">
            <img src="/assets/img/risingbiz-logo.png" alt="RisingBiz">
        </div>

        <?php if (!empty($error)): ?>
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
                >
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input
                    type="password"
                    name="password"
                    required
                >
            </div>

            <button class="btn btn-primary btn-block">
                Ingresar
            </button>
        </form>

    </div>
</div>

</body>
</html>
