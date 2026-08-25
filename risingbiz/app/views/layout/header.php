<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'RisingBiz' ?></title>
    <link rel="stylesheet" href="/assets/css/risingbiz.css">
</head>
<body>

<header class="topbar">
    <div class="brand">
        <img src="/assets/img/risingbiz-logo.png" alt="RisingBiz" style="height:40px;">
    </div>

    <nav class="menu">
        <a href="/dashboard.php">Dashboard</a>
        <a href="/clientes.php">Clientes</a>
        <a href="/productos.php">Productos</a>
        <a href="/ventas.php">Ventas</a>
        <a href="insumos.php">Insumos</a>
        <a href="/usuarios.php">Usuarios</a>
    </nav>

    <div class="user">
        <a href="/logout.php">Salir</a>
    </div>
</header>

<main class="container">
