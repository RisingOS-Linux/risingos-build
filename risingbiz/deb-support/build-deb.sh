#!/usr/bin/env bash
# build-deb.sh — Empaqueta RisingBiz como .deb instalable
# Correr desde la raíz del proyecto:
#   ~/Mis_Desarrollos/Rising_Arrow/RisingOS/risingos-build/risingbiz
set -euo pipefail

# Fijo el umask acá (no confiar en el del shell de cada máquina/usuario):
# el proyecto vive en un directorio compartido entre varios sistemas
# via el grupo "devs", así que todo lo que este script genere debe quedar
# escribible por el grupo para que cualquiera pueda reconstruir o limpiar
# sin pelearse con permisos heredados de otra máquina.
umask 002

PKG_NAME="risingbiz"
VERSION="1.0.4"
ARCH="all"
MAINTAINER="Francisco Galaso <franciscogalaso@rising-arrow.com>"
PROJECT_DIR="$(pwd)"
BUILD_ROOT="${PROJECT_DIR}/deb-build"
PKG_DIR="${BUILD_ROOT}/${PKG_NAME}_${VERSION}_${ARCH}"

echo "== Limpiando build anterior =="
rm -rf "${BUILD_ROOT}"
mkdir -p "${PKG_DIR}"

echo "== Creando estructura de directorios =="
mkdir -p "${PKG_DIR}/DEBIAN"
mkdir -p "${PKG_DIR}/usr/share/${PKG_NAME}"
mkdir -p "${PKG_DIR}/etc/${PKG_NAME}"
mkdir -p "${PKG_DIR}/etc/apache2/sites-available"
mkdir -p "${PKG_DIR}/usr/share/applications"
mkdir -p "${PKG_DIR}/usr/share/doc/${PKG_NAME}"
mkdir -p "${PKG_DIR}/usr/lib/systemd/system"
mkdir -p "${PKG_DIR}/usr/lib/${PKG_NAME}"
for size in 16 22 24 32 48 64 128 256; do
    mkdir -p "${PKG_DIR}/usr/share/icons/hicolor/${size}x${size}/apps"
done
mkdir -p "${PKG_DIR}/usr/share/icons/hicolor/512x512/apps"

echo "== Copiando código de la aplicación =="
cp -r "${PROJECT_DIR}/app" "${PKG_DIR}/usr/share/${PKG_NAME}/"
cp -r "${PROJECT_DIR}/public" "${PKG_DIR}/usr/share/${PKG_NAME}/"
cp "${PROJECT_DIR}/bootstrap.php" "${PKG_DIR}/usr/share/${PKG_NAME}/"
cp "${PROJECT_DIR}/schema.sql" "${PKG_DIR}/usr/share/${PKG_NAME}/"

# La carpeta storage/ del código fuente NO se empaqueta: los datos reales
# van a vivir en /var/lib/risingbiz, separados del código (se crean en postinst).
rm -rf "${PKG_DIR}/usr/share/${PKG_NAME}/public/../storage" 2>/dev/null || true

echo "== Copiando íconos al tema hicolor =="
for size in 16 22 24 32 48 64 128 256; do
    src="${PROJECT_DIR}/public/assets/img/icon-${size}.png"
    if [ -f "$src" ]; then
        cp "$src" "${PKG_DIR}/usr/share/icons/hicolor/${size}x${size}/apps/${PKG_NAME}.png"
    fi
done
if [ -f "${PROJECT_DIR}/public/assets/img/icon-512.png" ]; then
    cp "${PROJECT_DIR}/public/assets/img/icon-512.png" \
       "${PKG_DIR}/usr/share/icons/hicolor/512x512/apps/${PKG_NAME}.png"
fi

echo "== Generando control =="
cat > "${PKG_DIR}/DEBIAN/control" << EOF
Package: ${PKG_NAME}
Version: ${VERSION}
Section: web
Priority: optional
Architecture: ${ARCH}
Depends: apache2, libapache2-mod-php, php-sqlite3, php-gd, php-curl, php-intl, php-mbstring, php-xml, php-zip, sqlite3
Maintainer: ${MAINTAINER}
Description: RisingBiz - gestión de clientes, productos, ventas e insumos
 Aplicación web de gestión (MVC, PHP + SQLite) para pequeños negocios.
 Parte del ecosistema RisingOS.
EOF

echo "== Generando conffiles =="
cat > "${PKG_DIR}/DEBIAN/conffiles" << EOF
/etc/${PKG_NAME}/config.php
/etc/apache2/sites-available/${PKG_NAME}.conf
EOF

echo "== Generando archivo de configuración (/etc/risingbiz/config.php) =="
cat > "${PKG_DIR}/etc/${PKG_NAME}/config.php" << 'EOF'
<?php
declare(strict_types=1);

// Ruta a la base de datos de RisingBiz.
// No editar salvo que sepas lo que hacés: mover esto requiere migrar
// también el archivo .sqlite existente en /var/lib/risingbiz/.
define('RISINGBIZ_DB_PATH', '/var/lib/risingbiz/database.sqlite');
EOF

echo "== Generando vhost de Apache =="
cat > "${PKG_DIR}/etc/apache2/sites-available/${PKG_NAME}.conf" << EOF
<VirtualHost *:80>
    ServerName ${PKG_NAME}.local
    DocumentRoot /usr/share/${PKG_NAME}/public

    <Directory /usr/share/${PKG_NAME}/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/${PKG_NAME}_error.log
    CustomLog \${APACHE_LOG_DIR}/${PKG_NAME}_access.log combined
</VirtualHost>
EOF

echo "== Generando lanzador .desktop =="
cat > "${PKG_DIR}/usr/share/applications/${PKG_NAME}.desktop" << EOF
[Desktop Entry]
Type=Application
Name=RisingBiz
Comment=Gestión de clientes, productos, ventas e insumos
Exec=xdg-open http://${PKG_NAME}.local/
Icon=${PKG_NAME}
Categories=Office;Finance;
Terminal=false
EOF

echo "== Copiando copyright (GPL-3.0) =="
cp "${PROJECT_DIR}/deb-support/copyright" "${PKG_DIR}/usr/share/doc/${PKG_NAME}/copyright"
gzip -9 -n -c "${PROJECT_DIR}/CHANGELOG" > "${PKG_DIR}/usr/share/doc/${PKG_NAME}/changelog.gz" 2>/dev/null || true

echo "== Copiando servicio systemd (auto-reparación de /etc/hosts) =="
cp "${PROJECT_DIR}/deb-support/risingbiz-hosts.service" "${PKG_DIR}/usr/lib/systemd/system/risingbiz-hosts.service"
cp "${PROJECT_DIR}/deb-support/ensure-hosts-entry.sh" "${PKG_DIR}/usr/lib/${PKG_NAME}/ensure-hosts-entry.sh"
chmod 755 "${PKG_DIR}/usr/lib/${PKG_NAME}/ensure-hosts-entry.sh"

echo "== Copiando postinst / postrm =="
cp "${PROJECT_DIR}/deb-support/postinst" "${PKG_DIR}/DEBIAN/postinst"
cp "${PROJECT_DIR}/deb-support/postrm" "${PKG_DIR}/DEBIAN/postrm"
chmod 755 "${PKG_DIR}/DEBIAN/postinst" "${PKG_DIR}/DEBIAN/postrm"

echo "== Fijando permisos del árbol del paquete =="
find "${PKG_DIR}" -type d -exec chmod 755 {} \;
find "${PKG_DIR}" -type f -exec chmod 644 {} \;
chmod 755 "${PKG_DIR}/DEBIAN/postinst" "${PKG_DIR}/DEBIAN/postrm"
chmod 755 "${PKG_DIR}/usr/lib/${PKG_NAME}/ensure-hosts-entry.sh"
# Limpieza explícita de setuid/setgid heredados del entorno (algunos
# sistemas aplican setgid por defecto en directorios nuevos vía umask/ACL,
# y dpkg-deb rechaza el directorio DEBIAN si tiene ese bit puesto).
chmod -R go-s,u-s "${PKG_DIR}"
chmod 755 "${PKG_DIR}/DEBIAN"

echo "== Construyendo el .deb =="
dpkg-deb --build --root-owner-group "${PKG_DIR}"

echo ""
echo "Listo: ${PKG_DIR}.deb"
echo "Para instalar: sudo apt install ${PKG_DIR}.deb"
