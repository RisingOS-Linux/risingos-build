CREATE TABLE clientes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL,
    email TEXT,
    telefono TEXT,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
, estado TEXT DEFAULT 'Nuevo', fecha_estado DATETIME);
CREATE TABLE productos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL,
    costo REAL NOT NULL,
    precio REAL NOT NULL,
    stock INTEGER DEFAULT 0,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE movimientos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo TEXT CHECK(tipo IN ('compra','venta')) NOT NULL,
    producto_id INTEGER NOT NULL,
    cantidad INTEGER NOT NULL,
    total REAL NOT NULL,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP, cliente_id INTEGER,
    FOREIGN KEY(producto_id) REFERENCES productos(id)
);
CREATE TABLE usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);
CREATE TABLE insumos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item TEXT NOT NULL,
    proveedor TEXT,
    cantidad INTEGER NOT NULL,
    costo_unitario REAL NOT NULL,
    costo_total REAL NOT NULL,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);
