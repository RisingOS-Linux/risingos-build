# Repositorio APT de RisingOS
## repo.rising-arrow.online

---

## Estructura del proyecto

```
apt-repo/
├── scripts/
│   ├── build-repo.sh       ← genera y firma el repo
│   ├── upload-repo.sh      ← sube al hosting via FTP
│   └── export-gpg-key.sh  ← exporta la clave GPG pública
├── repo/                   ← generado por build-repo.sh
│   ├── pool/
│   │   └── main/
│   │       ├── rising-welcome_1.0.0_all.deb
│   │       └── rising-ia_1.0.0_all.deb
│   └── dists/
│       └── stable/
│           ├── InRelease
│           ├── Release
│           ├── Release.gpg
│           └── main/
│               └── binary-amd64/
│                   ├── Packages
│                   └── Packages.gz
├── keys/                   ← generado por export-gpg-key.sh
│   ├── risingos.gpg        ← clave binaria (para la ISO)
│   └── risingos.asc        ← clave ASCII (para publicar)
└── risingos.list           ← sources.list para la ISO
```

---

## Flujo completo: publicar una nueva versión

### 1. Instalar dependencias (una sola vez)
```bash
sudo apt install dpkg-dev apt-utils gnupg lftp
```

### 2. Exportar la clave GPG (una sola vez)
```bash
chmod +x scripts/export-gpg-key.sh
./scripts/export-gpg-key.sh
```

### 3. Agregar los .deb al pool
```bash
cp ~/risingos-build/rising-welcome_1.0.0_all.deb repo/pool/main/
cp ~/risingos-build/rising-ia_1.0.0_all.deb      repo/pool/main/
```

### 4. Generar y firmar el repo
```bash
chmod +x scripts/build-repo.sh
./scripts/build-repo.sh
```

### 5. Configurar credenciales FTP (una sola vez)
```bash
cat > ~/.risingos-ftp << 'EOF'
FTP_USER='tu_usuario_donweb'
FTP_PASS='tu_contraseña_donweb'
EOF
chmod 600 ~/.risingos-ftp
```

### 6. Subir al hosting
```bash
chmod +x scripts/upload-repo.sh
./scripts/upload-repo.sh
```

### 7. Verificar que el repo está online
```bash
curl -I https://repo.rising-arrow.online/dists/stable/InRelease
```

---

## Integrar en la ISO (live-build)

### Clave GPG
```bash
mkdir -p config/includes.chroot/usr/share/keyrings/
cp apt-repo/keys/risingos.gpg \
   config/includes.chroot/usr/share/keyrings/risingos.gpg
```

### sources.list
```bash
mkdir -p config/includes.chroot/etc/apt/sources.list.d/
cp apt-repo/risingos.list \
   config/includes.chroot/etc/apt/sources.list.d/risingos.list
```

---

## Para usuarios que instalen RisingOS

Agregar el repositorio manualmente:
```bash
curl -fsSL https://repo.rising-arrow.online/risingos.asc | \
  sudo gpg --dearmor -o /usr/share/keyrings/risingos.gpg

echo "deb [signed-by=/usr/share/keyrings/risingos.gpg] \
  https://repo.rising-arrow.online stable main" | \
  sudo tee /etc/apt/sources.list.d/risingos.list

sudo apt update
```

---

## Clave GPG

- **ID**: `5E8D42A895F5943D`
- **Fingerprint**: `0D637544D88A900D93A445D25E8D42A895F5943D`
- **Tipo**: RSA 4096 bits
- **UID**: Rising OS (Rising OS Official Repository) <repo@rising-arrow.com>
- **URL pública**: https://repo.rising-arrow.online/risingos.asc
