#!/bin/bash
set -euo pipefail

FLAVOR="${1:-}"
DRY_RUN="${2:-}"

if [[ "${FLAVOR}" != "xfce" && "${FLAVOR}" != "gnome" ]]; then
    echo "Uso: $0 <xfce|gnome> [--dry-run]"
    exit 1
fi

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${REPO_ROOT}"

echo "[build] === Flavor seleccionado: ${FLAVOR} ==="

echo "[build] Limpiando symlinks/copias de builds previos..."
find config/package-lists -maxdepth 1 -type l -delete 2>/dev/null || true
for link in config/hooks/normal/*; do
    [ -L "$link" ] || continue
    target="$(readlink "$link")"
    if [[ "$target" == ../flavors/* ]]; then
        rm "$link"
    fi
done
# Limpiar CUALQUIER archivo que algún flavor pueda haber copiado antes,
# sin importar cual estaba activo en el build anterior. Esto evita que
# un archivo suelto (ej: .profile) quede pisando includes.chroot
# compartido para siempre, ya que el cp -r de abajo solo agrega/sobreescribe,
# nunca resta.
for flavor_dir in config/hooks/flavors/*/includes.chroot; do
    [ -d "$flavor_dir" ] || continue
    while IFS= read -r relpath; do
        rm -f "config/includes.chroot/${relpath#./}"
    done < <(cd "$flavor_dir" && find . -type f)
done
# Limpiar directorios que hayan quedado vacíos tras el borrado de arriba
find config/includes.chroot -type d -empty -delete 2>/dev/null || true

ln -s "../package-lists-flavors/${FLAVOR}.list.chroot" "config/package-lists/${FLAVOR}.list.chroot"
echo "[build] Symlink: package-lists/${FLAVOR}.list.chroot -> package-lists-flavors/${FLAVOR}.list.chroot"

if [ -d "config/hooks/flavors/${FLAVOR}" ]; then
    for f in config/hooks/flavors/"${FLAVOR}"/*.hook.chroot; do
        [ -e "$f" ] || continue
        ln -s "../flavors/${FLAVOR}/$(basename "$f")" "config/hooks/normal/$(basename "$f")"
        echo "[build] Symlink hook: $(basename "$f")"
    done
fi

if [ -d "config/hooks/flavors/${FLAVOR}/includes.chroot" ]; then
    cp -r config/hooks/flavors/"${FLAVOR}"/includes.chroot/. config/includes.chroot/
    echo "[build] includes.chroot de ${FLAVOR} copiado sobre config/includes.chroot/"
fi

if [ "${DRY_RUN}" == "--dry-run" ]; then
    echo "[build] === DRY RUN: preparación completa, NO se ejecuta lb build ==="
    exit 0
fi

echo "[build] === Preparación completa, iniciando lb build ==="
sudo lb clean --purge
sudo lb config --bootappend-live "boot=live components quiet splash locales=es_AR.UTF-8"
sudo lb build 2>&1 | tee "build-${FLAVOR}-$(date +%Y%m%d-%H%M%S).log"
