#!/bin/bash
# build-repo.sh — Genera y firma el repositorio APT de RisingOS
# Uso: ./build-repo.sh
# Requiere: dpkg-dev, apt-utils, gnupg
#
# Rising Arrow / Francisco Galaso

set -e

# ── Configuración ─────────────────────────────────────────────────────────────
GPG_KEY_ID="5E8D42A895F5943D"
REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)/repo"
POOL_DIR="$REPO_DIR/pool/main"
DISTS_DIR="$REPO_DIR/dists/stable/main/binary-amd64"
RELEASE_DIR="$REPO_DIR/dists/stable"
ORIGIN="RisingOS"
LABEL="RisingOS"
SUITE="stable"
CODENAME="trixie"
COMPONENTS="main"
ARCH="amd64"
DESCRIPTION="Repositorio oficial de RisingOS - Rising Arrow"

# ── Colores ───────────────────────────────────────────────────────────────────
GREEN='\033[0;32m'
ORANGE='\033[0;33m'
RED='\033[0;31m'
NC='\033[0m'

log()  { echo -e "${GREEN}[RisingOS Repo]${NC} $1"; }
warn() { echo -e "${ORANGE}[WARN]${NC} $1"; }
err()  { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

# ── Verificar dependencias ────────────────────────────────────────────────────
for cmd in dpkg-scanpackages apt-ftparchive gpg gzip; do
    command -v "$cmd" >/dev/null 2>&1 || err "Falta: $cmd — instalá dpkg-dev apt-utils gnupg"
done

# ── Verificar clave GPG ───────────────────────────────────────────────────────
gpg --list-secret-keys "$GPG_KEY_ID" >/dev/null 2>&1 || \
    err "Clave GPG $GPG_KEY_ID no encontrada en el keyring"

# ── Crear estructura de directorios ───────────────────────────────────────────
log "Creando estructura del repositorio..."
mkdir -p "$POOL_DIR"
mkdir -p "$DISTS_DIR"
mkdir -p "$RELEASE_DIR"

# ── Verificar que hay .deb en el pool ─────────────────────────────────────────
DEB_COUNT=$(find "$POOL_DIR" -name "*.deb" | wc -l)
if [ "$DEB_COUNT" -eq 0 ]; then
    warn "No hay .deb en $POOL_DIR"
    warn "Copiá los .deb antes de continuar:"
    warn "  cp rising-welcome_1.0.0_all.deb $POOL_DIR/"
    warn "  cp rising-ia_1.0.0_all.deb      $POOL_DIR/"
    exit 1
fi
log "Encontrados $DEB_COUNT paquete(s) en el pool"

# ── Generar índice Packages ───────────────────────────────────────────────────
log "Generando índice Packages..."
cd "$REPO_DIR"
dpkg-scanpackages --arch amd64 pool/main /dev/null > dists/stable/main/binary-amd64/Packages
gzip -k -f dists/stable/main/binary-amd64/Packages
log "Packages generado: $(wc -l < dists/stable/main/binary-amd64/Packages) líneas"

# ── Generar Release ───────────────────────────────────────────────────────────
log "Generando Release..."
cd "$RELEASE_DIR"

apt-ftparchive \
    -o "APT::FTPArchive::Release::Origin=$ORIGIN" \
    -o "APT::FTPArchive::Release::Label=$LABEL" \
    -o "APT::FTPArchive::Release::Suite=$SUITE" \
    -o "APT::FTPArchive::Release::Codename=$CODENAME" \
    -o "APT::FTPArchive::Release::Architectures=$ARCH" \
    -o "APT::FTPArchive::Release::Components=$COMPONENTS" \
    -o "APT::FTPArchive::Release::Description=$DESCRIPTION" \
    release . > Release

log "Release generado"

# ── Firmar con GPG ────────────────────────────────────────────────────────────
log "Firmando con GPG ($GPG_KEY_ID)..."

# Release.gpg — firma detached
gpg --default-key "$GPG_KEY_ID" \
    --armor \
    --detach-sign \
    --output Release.gpg \
    Release

# InRelease — firma inline (el que usa apt por defecto)
gpg --default-key "$GPG_KEY_ID" \
    --armor \
    --clearsign \
    --output InRelease \
    Release

log "Repo firmado correctamente"

# ── Resumen ───────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}══════════════════════════════════════════${NC}"
echo -e "${GREEN}  Repositorio APT generado y firmado ✓${NC}"
echo -e "${GREEN}══════════════════════════════════════════${NC}"
echo ""
echo "  Pool:"
find "$POOL_DIR" -name "*.deb" | while read f; do
    echo "    $(basename $f)"
done
echo ""
echo "  Subí el contenido de repo/ a tu hosting:"
echo "    ftp repo.rising-arrow.online"
echo "    → subir todo el contenido de repo/"
echo ""
echo "  Línea para sources.list:"
echo "    deb [signed-by=/usr/share/keyrings/risingos.gpg] https://repo.rising-arrow.online stable main"
echo ""
