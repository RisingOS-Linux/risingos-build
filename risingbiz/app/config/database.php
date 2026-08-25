<?php
declare(strict_types=1);

// En producción (paquete .deb instalado), la ruta viene de /etc/risingbiz/config.php.
// En desarrollo, si ese archivo no existe, usamos storage/database.sqlite local.

$systemConfig = '/etc/risingbiz/config.php';

if (is_file($systemConfig)) {
    require $systemConfig; // debe definir RISINGBIZ_DB_PATH
} else {
    define('RISINGBIZ_DB_PATH', __DIR__ . '/../../storage/database.sqlite');
}
