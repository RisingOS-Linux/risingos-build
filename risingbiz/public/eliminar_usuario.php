<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: usuarios.php');
    exit;
}

$id = (int) $_POST['id'];

$total = (int) $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
if ($total <= 1) {
    header('Location: usuarios.php?error=ultimo');
    exit;
}

$stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->execute([$id]);

// Si te eliminaste a vos mismo, cerrá tu propia sesión
if ((int) ($_SESSION['user_id'] ?? 0) === $id) {
    session_destroy();
    header('Location: login.php');
    exit;
}

header('Location: usuarios.php?ok=eliminado');
exit;
