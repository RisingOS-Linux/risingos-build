<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/app/config/database.php';
$db = new PDO('sqlite:' . RISINGBIZ_DB_PATH);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$public_pages = ['login.php', 'setup.php'];

$current = basename($_SERVER['PHP_SELF']);

// Si todavía no existe ningún usuario, forzar la configuración inicial
// (primer acceso tras instalar el .deb, sin importar si fue por terminal
// o por una tienda de software gráfica).
$total_usuarios = (int) $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
if ($total_usuarios === 0 && $current !== 'setup.php') {
    header('Location: setup.php');
    exit;
}

if (!in_array($current, $public_pages) && empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
