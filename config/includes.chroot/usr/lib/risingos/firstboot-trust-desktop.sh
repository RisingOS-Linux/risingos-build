#!/usr/bin/env bash
# RisingOS — Firstboot trust desktop launchers
# Marca como confiables (metadata::xfce-exe-checksum) los lanzadores .desktop
# ubicados en el Escritorio, para evitar el diálogo "Lanzador de aplicaciones
# no confiable" de Thunar/XFCE 4.18+. Se ejecuta una sola vez por usuario.
#
# Referencia: desde Thunar 4.18, el bit ejecutable (chmod +x) ya no alcanza
# para confiar en un .desktop — Thunar requiere además un checksum SHA256
# guardado vía gvfs-metadata (metadata::xfce-exe-checksum). Ese checksum solo
# puede setearse con una sesión de usuario activa (gvfs), por eso este script
# corre como autostart de primer login y no puede aplicarse durante el build.

STAMP="$HOME/.config/risingos/.desktop-trusted"

# Salir si ya se ejecutó para este usuario
[ -f "$STAMP" ] && exit 0

# Verificar que gio esté disponible
if ! command -v gio >/dev/null 2>&1; then
    echo "risingos-trust-desktop: gio no disponible, abortando" >&2
    exit 1
fi

DESKTOP_DIR="$HOME/Desktop"

if [ -d "$DESKTOP_DIR" ]; then
    for f in "$DESKTOP_DIR"/*.desktop; do
        [ -f "$f" ] || continue
        CHECKSUM=$(sha256sum "$f" | awk '{print $1}')
        gio set -t string "$f" metadata::xfce-exe-checksum "$CHECKSUM" 2>/dev/null
        # metadata::trusted es de GNOME; Thunar no la usa pero no hace daño agregarla
        gio set -t string "$f" metadata::trusted "true" 2>/dev/null
        touch "$f"
    done
fi

# Marcar como ejecutado
mkdir -p "$(dirname "$STAMP")"
touch "$STAMP"
