<?php
/**
 * ==========================================================================
 * ParkControl · database/migrate.php
 * ==========================================================================
 * Aplicador de migraciones y datos iniciales por consola.
 *
 *   C:\xampp\php\php.exe database\migrate.php
 *   C:\xampp\php\php.exe database\migrate.php --estado
 *
 * Registra cada archivo aplicado en `schema_migrations`, de modo que una
 * segunda ejecución no repite nada. No borra datos.
 *
 * Nota: las migraciones .sql se ejecutan sin transacción porque MySQL hace
 * COMMIT implícito en cada DDL (ALTER/CREATE).
 * ==========================================================================
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Este script solo se ejecuta por consola.');
}

require __DIR__ . '/../app/config/conexion.php';

function linea(string $texto = ''): void
{
    echo $texto . PHP_EOL;
}

try {
    $pdo = Database::getConexion();
} catch (Throwable $error) {
    linea('ERROR de conexión: ' . $error->getMessage());
    linea('Revisa app/config/config.local.php (usuario y clave de MySQL).');
    exit(1);
}

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS schema_migrations (
        id INT(11) NOT NULL AUTO_INCREMENT,
        archivo VARCHAR(150) NOT NULL,
        aplicadoEn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_schema_archivo (archivo)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$yaAplicada = static function (PDO $pdo, string $archivo): bool {
    $stmt = $pdo->prepare('SELECT 1 FROM schema_migrations WHERE archivo = ? LIMIT 1');
    $stmt->execute([$archivo]);
    return (bool) $stmt->fetchColumn();
};

$registrar = static function (PDO $pdo, string $archivo): void {
    $stmt = $pdo->prepare('INSERT INTO schema_migrations (archivo) VALUES (?)');
    $stmt->execute([$archivo]);
};

/** Divide un archivo .sql en sentencias y las ejecuta. */
$ejecutarSql = static function (PDO $pdo, string $sql): void {
    $sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
    $sentencias = preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: [];
    foreach ($sentencias as $sentencia) {
        $sentencia = trim($sentencia);
        if ($sentencia !== '') {
            $pdo->exec($sentencia);
        }
    }
};

if (in_array('--estado', $argv, true)) {
    linea('Migraciones registradas:');
    foreach ($pdo->query('SELECT archivo, aplicadoEn FROM schema_migrations ORDER BY id')->fetchAll() as $fila) {
        linea(sprintf('  · %-42s %s', $fila['archivo'], $fila['aplicadoEn']));
    }
    exit(0);
}

linea('ParkControl · migraciones');
linea(str_repeat('-', 60));

$migraciones = glob(__DIR__ . '/migrations/*.sql') ?: [];
sort($migraciones);

foreach ($migraciones as $ruta) {
    $archivo = 'migrations/' . basename($ruta);
    if ($yaAplicada($pdo, $archivo)) {
        linea('  = ' . $archivo . ' (ya aplicada)');
        continue;
    }

    try {
        $ejecutarSql($pdo, (string) file_get_contents($ruta));
        $registrar($pdo, $archivo);
        linea('  + ' . $archivo . ' aplicada');
    } catch (Throwable $error) {
        linea('  ! ' . $archivo . ' FALLÓ: ' . $error->getMessage());
        exit(1);
    }
}

$semillas = glob(__DIR__ . '/seeds/*.php') ?: [];
sort($semillas);

foreach ($semillas as $ruta) {
    $archivo = 'seeds/' . basename($ruta);
    if ($yaAplicada($pdo, $archivo)) {
        linea('  = ' . $archivo . ' (ya aplicada)');
        continue;
    }

    try {
        $pdo->beginTransaction();
        require $ruta;
        $registrar($pdo, $archivo);
        $pdo->commit();
        linea('  + ' . $archivo . ' aplicada');
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        linea('  ! ' . $archivo . ' FALLÓ: ' . $error->getMessage());
        exit(1);
    }
}

linea(str_repeat('-', 60));
linea('Listo. Base de datos al día.');
