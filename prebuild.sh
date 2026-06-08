#!/bin/bash
set -e
BUILDDIR="$(dirname "$(realpath "$0")")"
THEMEDIR="$HOME/Mis_Desarrollos/Rising_Arrow/RisingOS/risingos-theme"

echo "[RisingOS] Preparando assets de LightDM..."
mkdir -p "$BUILDDIR/config/includes.chroot/usr/share/risingos/lightdm"
cp "$THEMEDIR/lightdm/risingos-logo.png" \
   "$BUILDDIR/config/includes.chroot/usr/share/risingos/lightdm/risingos-logo.png"
echo "[RisingOS] Assets LightDM OK"
