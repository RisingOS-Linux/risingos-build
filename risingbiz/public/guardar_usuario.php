<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios.php');
    exit;
}

$id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

if ($usuario === '') {
    header('Location: usuario.php?id=' . ($id ?? '') . '&error=' . urlencode('El usuario no puede estar vacío.'));
    exit;
}

if ($password !== '' || $confirmar !== '' || !$id) {
    if (strlen($password) < 8) {
        header('Location: usuario.php?id=' . ($id ?? '') . '&error=' . urlencode('La contraseña debe tener al menos 8 caracteres.'));
        exit;
    }
    if ($password !== $confirmar) {
        header('Location: usuario.php?id=' . ($id ?? '') . '&error=' . urlencode('Las contraseñas no coinciden.'));
        exit;
    }
}

try {
    if ($id) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE usuarios SET usuario = ?, password = ? WHERE id = ?");
            $stmt->execute([$usuario, $hash, $id]);
        } else {
            $stmt = $db->prepare("UPDATE usuarios SET usuario = ? WHERE id = ?");
            $stmt->execute([$usuario, $id]);
        }
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
        $stmt->execute([$usuario, $hash]);
    }
} catch (PDOException $e) {
    header('Location: usuario.php?id=' . ($id ?? '') . '&error=' . urlencode('Ese nombre de usuario ya existe.'));
    exit;
}

header('Location: usuarios.php?ok=1');
exit;
