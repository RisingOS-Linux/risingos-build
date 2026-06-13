#!/bin/bash
# export-gpg-key.sh — Exporta la clave pública GPG para incluir en la ISO
# y para que los usuarios puedan agregarla manualmente
#
# Rising Arrow / Francisco Galaso

set -e

GPG_KEY_ID="5E8D42A895F5943D"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
OUT_DIR="$SCRIPT_DIR/../keys"

GREEN='\033[0;32m'
NC='\033[0m'
log() { echo -e "${GREEN}[GPG]${NC} $1"; }

mkdir -p "$OUT_DIR"

# Exportar en formato binario (para /usr/share/keyrings/)
gpg --export "$GPG_KEY_ID" > "$OUT_DIR/risingos.gpg"
log "Exportada: keys/risingos.gpg (binario — para /usr/share/keyrings/)"

# Exportar en formato ASCII armored (para mostrar/descargar)
gpg --armor --export "$GPG_KEY_ID" > "$OUT_DIR/risingos.asc"
log "Exportada: keys/risingos.asc (ASCII — para publicar en el repo)"

echo ""
echo "  Copiá risingos.gpg a la ISO:"
echo "    cp keys/risingos.gpg \\"
echo "       config/includes.chroot/usr/share/keyrings/risingos.gpg"
echo ""
echo "  Publicá risingos.asc en:"
echo "    https://repo.rising-arrow.online/risingos.asc"
echo ""
echo "  Los usuarios pueden agregarla con:"
echo "    curl -fsSL https://repo.rising-arrow.online/risingos.asc | \\"
echo "      sudo gpg --dearmor -o /usr/share/keyrings/risingos.gpg"
