<?php
/**
 * ==========================================================================
 * ParkControl · app/config/conexion.php
 * ==========================================================================
 * Conexión PDO única (singleton) a MySQL/MariaDB.
 *
 * - Lee las credenciales desde config.php + config.local.php.
 * - Usa consultas preparadas reales (EMULATE_PREPARES = false).
 * - No imprime errores: las excepciones las maneja el front controller.
 * ==========================================================================
 */

require_once __DIR__ . '/config.php';

class Database
{
    private static ?PDO $conexion = null;
    private static ?array $config = null;

    /** Configuración completa de la aplicación (app + db). */
    public static function configuracion(): array
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/config.php';
        }
        return self::$config;
    }

    /** Devuelve la conexión PDO compartida, creándola la primera vez. */
    public static function getConexion(): PDO
    {
        if (self::$conexion instanceof PDO) {
            return self::$conexion;
        }

        $db = self::configuracion()['db'];
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['puerto'],
            $db['nombre'],
            $db['charset']
        );

        self::$conexion = new PDO($dsn, $db['usuario'], $db['clave'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$conexion;
    }
}
