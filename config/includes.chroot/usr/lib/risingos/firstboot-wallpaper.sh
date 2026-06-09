#!/usr/bin/env bash
# RisingOS — Firstboot wallpaper setup
# Configura el wallpaper andino para todos los monitores/workspaces reales
# detectados en tiempo de ejecución. Se ejecuta una sola vez por usuario.
#
# XFCE 4.20 usa la clave:
#   /backdrop/screen0/monitor{CONECTOR}/workspace{N}/last-image
# donde {CONECTOR} es el nombre exacto del conector según xrandr
# (Virtual-1, eDP-1, HDMI-1, DP-1, etc.) — sin transformación alguna.
# Confirmado en xfdesktop-backdrop-manager.c línea 363.

WALLPAPER='/usr/share/backgrounds/xfce/risingos/Dramatic_frozen_andes.png'
STAMP="$HOME/.config/risingos/.wallpaper-configured"

# Salir si ya se ejecutó para este usuario
[ -f "$STAMP" ] && exit 0

# Esperar a que xfdesktop esté listo (hasta 10 segundos)
for i in $(seq 1 20); do
    pgrep -x xfdesktop >/dev/null 2>&1 && break
    sleep 0.5
done

# Número de workspaces (fallback: 1)
WSPCNT=$(xfconf-query --channel xfwm4 --property /general/workspace_count 2>/dev/null)
[[ "$WSPCNT" =~ ^[0-9]+$ ]] || WSPCNT=1

# Iterar sobre cada monitor conectado
while IFS= read -r MONITOR; do
    for ((WSP=0; WSP < WSPCNT; WSP++)); do
        BASE="/backdrop/screen0/monitor${MONITOR}/workspace${WSP}"

        xfconf-query --channel xfce4-desktop \
            --property "${BASE}/last-image" \
            --create --type string --set "$WALLPAPER"

        xfconf-query --channel xfce4-desktop \
            --property "${BASE}/image-style" \
            --create --type int --set 5

        xfconf-query --channel xfce4-desktop \
            --property "${BASE}/color-style" \
            --create --type int --set 0
    done
done < <(xrandr --query | awk '/ connected/{print $1}')

# Marcar como ejecutado
mkdir -p "$(dirname "$STAMP")"
touch "$STAMP"
