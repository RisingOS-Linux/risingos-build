<?php
require_once __DIR__ . '/../bootstrap.php';

/* Seguridad mínima */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: clientes.php');
    exit;
}

/* Datos */
$id       = $_POST['id'] ?? null;
$nombre   = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email    = trim($_POST['email'] ?? '');
$estado   = $_POST['estado'] ?? 'Nuevo';

/* Validación básica */
if ($nombre === '') {
    die('El nombre es obligatorio');
}

/* EDICIÓN */
if ($id) {

    // Estado anterior
    $stmt = $db->prepare("SELECT estado FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $estado_anterior = $stmt->fetchColumn();

    if (!$estado_anterior) {
        die('Cliente no encontrado');
    }

    // Si cambia el estado, actualizamos fecha_estado
    if ($estado !== $estado_anterior) {
        $stmt = $db->prepare("
            UPDATE clientes
            SET nombre = ?, telefono = ?, email = ?, estado = ?, fecha_estado = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$nombre, $telefono, $email, $estado, $id]);
    } else {
        $stmt = $db->prepare("
            UPDATE clientes
            SET nombre = ?, telefono = ?, email = ?, estado = ?
            WHERE id = ?
        ");
        $stmt->execute([$nombre, $telefono, $email, $estado, $id]);
    }

/* ALTA */
} else {

    $stmt = $db->prepare("
        INSERT INTO clientes (nombre, telefono, email, estado, fecha_estado)
        VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
    ");
    $stmt->execute([$nombre, $telefono, $email, $estado]);
}

/* Volvemos al listado */
header('Location: clientes.php');
exit;
