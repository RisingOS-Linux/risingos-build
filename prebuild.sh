#!/bin/bash
set -e
BUILDDIR="$(dirname "$(realpath "$0")")"
THEMEDIR="$HOME/Mis_Desarrollos/Rising_Arrow/RisingOS/risingos-theme"

echo "[RisingOS] Preparando assets de LightDM..."
mkdir -p "$BUILDDIR/config/includes.chroot/usr/share/risingos/lightdm"
cp "$THEMEDIR/lightdm/risingos-logo.png" \
   "$BUILDDIR/config/includes.chroot/usr/share/risingos/lightdm/risingos-logo.png"
echo "[RisingOS] Assets LightDM OK"

echo "[RisingOS] Limpiando build anterior..."
cd "$BUILDDIR"
sudo lb clean --purge

echo "[RisingOS] Ejecutando lb config..."
lb config \
  --distribution trixie \
  --archive-areas "main contrib non-free non-free-firmware" \
  --binary-images iso-hybrid \
  --bootloader grub-efi \
  --debian-installer none
echo "[RisingOS] lb config OK"
