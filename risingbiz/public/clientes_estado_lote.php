<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit;
}

$ids = $_POST['ids'] ?? [];
$estado = $_POST['estado'] ?? null;

if (empty($ids) || !$estado) {
    header('Location: clientes.php');
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));

$sql = "
    UPDATE clientes
    SET estado = ?, fecha_estado = datetime('now')
    WHERE id IN ($placeholders)
";

$stmt = $db->prepare($sql);
$stmt->execute(array_merge([$estado], $ids));

header('Location: clientes.php');
exit;
