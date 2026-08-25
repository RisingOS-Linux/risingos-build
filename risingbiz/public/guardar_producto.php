<?php
require_once __DIR__ . '/../bootstrap.php';

$stmt = $db->prepare("
    INSERT INTO productos (nombre, costo, precio, stock)
    VALUES (:nombre, :costo, :precio, :stock)
");

$stmt->execute([
    ':nombre' => $_POST['nombre'],
    ':costo' => $_POST['costo'],
    ':precio' => $_POST['precio'],
    ':stock' => $_POST['stock'],
]);

header('Location: productos.php');
exit;
