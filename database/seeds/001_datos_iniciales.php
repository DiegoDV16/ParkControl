<?php
/**
 * ==========================================================================
 * ParkControl · database/seeds/001_datos_iniciales.php
 * ==========================================================================
 * Datos base del sistema: roles, filas y plazas, tarifas, servicios extra,
 * parámetros de configuración y credenciales del administrador.
 *
 * Lo ejecuta database/migrate.php una sola vez. Es idempotente: usa
 * INSERT IGNORE sobre claves únicas.
 * ==========================================================================
 */

/** @var PDO $pdo */
$pdo = Database::getConexion();

$pdo->exec("INSERT IGNORE INTO rol (rol) VALUES ('Administrador'), ('Operador')");

$pdo->exec(
    "INSERT IGNORE INTO fila (fila, capacidadTotal, estado) VALUES
     ('A', 20, 1), ('B', 20, 1), ('C', 10, 1)"
);

$insertarPlaza = $pdo->prepare('INSERT IGNORE INTO plaza (idFila, numero, estado) VALUES (?, ?, 1)');
foreach ($pdo->query('SELECT idFila, fila, capacidadTotal FROM fila')->fetchAll() as $fila) {
    for ($numero = 1; $numero <= (int) $fila['capacidadTotal']; $numero++) {
        $insertarPlaza->execute([
            $fila['idFila'],
            $fila['fila'] . str_pad((string) $numero, 2, '0', STR_PAD_LEFT),
        ]);
    }
}

$pdo->exec(
    "INSERT IGNORE INTO tarifa (nombre, valorHora, fraccionMinutos, valorFraccion) VALUES
     ('Tarifa general', 1500.00, 15, 375.00),
     ('Tarifa nocturna', 2000.00, 60, 2000.00)"
);

$pdo->exec(
    "INSERT IGNORE INTO servicioextra (servicioExtra, valor, activo) VALUES
     ('Lavado básico', 5000.00, 1),
     ('Aspirado', 3000.00, 1),
     ('Agua y aire', 1000.00, 1)"
);

$pdo->exec(
    "INSERT IGNORE INTO configuracion (clave, valor, descripcion) VALUES
     ('nombre_estacionamiento', 'ParkControl', 'Nombre visible del estacionamiento'),
     ('direccion', 'Av. Siempre Viva 742', 'Dirección del recinto'),
     ('telefono', '+56 9 1234 5678', 'Teléfono de contacto'),
     ('moneda', 'CLP', 'Moneda usada en los cobros')"
);

// Credenciales del administrador: se conserva el usuario existente y solo se
// restablece su contraseña. Se puede fijar con PARKCONTROL_ADMIN_PASS.
$idAdmin = $pdo->query(
    "SELECT u.idUsuario
     FROM usuario u
     INNER JOIN rol r ON r.idRol = u.idRol
     WHERE r.rol = 'Administrador'
     ORDER BY u.idUsuario
     LIMIT 1"
)->fetchColumn();

$clave = getenv('PARKCONTROL_ADMIN_PASS');
$generada = false;
if (!is_string($clave) || $clave === '') {
    $clave = bin2hex(random_bytes(6));
    $generada = true;
}

$hash = password_hash($clave, PASSWORD_DEFAULT);

if ($idAdmin !== false && $idAdmin !== null) {
    $stmt = $pdo->prepare('UPDATE usuario SET contrasena = ? WHERE idUsuario = ?');
    $stmt->execute([$hash, (int) $idAdmin]);
    echo "  Contraseña del administrador existente restablecida.\n";
} else {
    $idRol = (int) $pdo->query("SELECT idRol FROM rol WHERE rol = 'Administrador' LIMIT 1")->fetchColumn();
    $stmt = $pdo->prepare(
        'INSERT INTO usuario (idRol, rut, nombres, apellidoPaterno, correo, contrasena, estado)
         VALUES (?, ?, ?, ?, ?, ?, 1)'
    );
    $stmt->execute([$idRol, '11.111.111-1', 'Administrador', 'General', 'admin@parkcontrol.cl', $hash]);
    echo "  Usuario administrador creado: admin@parkcontrol.cl\n";
}

if ($generada) {
    echo "  CLAVE TEMPORAL DEL ADMINISTRADOR: {$clave}\n";
    echo "  (cámbiala al iniciar sesión y no la versiones)\n";
}
