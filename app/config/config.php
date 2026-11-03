<?php
/**
 * ==========================================================================
 * ParkControl · app/config/config.php
 * ==========================================================================
 * Configuración base de la aplicación. Los valores sensibles (credenciales de
 * MySQL) viven en `config.local.php`, que está fuera del repositorio, o en
 * variables de entorno. Este archivo solo declara los valores por defecto.
 *
 * Orden de precedencia para cada valor:
 *   1. config.local.php  (define el array que se fusiona aquí)
 *   2. Variables de entorno PARKCONTROL_*
 *   3. Valor por defecto de este archivo
 * ==========================================================================
 */

$config = [
    'app' => [
        'nombre'         => 'ParkControl',
        'zona_horaria'   => 'America/Santiago',
        'moneda'         => 'CLP',
        'version'        => '1.0.0',
        // En producción cambiar a false para no mostrar errores internos.
        'depuracion'     => true,
        // Minutos de inactividad antes de cerrar la sesión.
        'sesion_minutos' => 30,
    ],
    'db' => [
        'host'    => getenv('PARKCONTROL_DB_HOST') ?: '127.0.0.1',
        'puerto'  => (int) (getenv('PARKCONTROL_DB_PORT') ?: 3306),
        'nombre'  => getenv('PARKCONTROL_DB_NAME') ?: 'parkcontrol',
        'usuario' => getenv('PARKCONTROL_DB_USER') ?: 'parkcontrol_app',
        'clave'   => getenv('PARKCONTROL_DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
];

// Configuración local (no versionada): credenciales reales del entorno.
$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $override = require $local;
    if (is_array($override)) {
        $config = array_replace_recursive($config, $override);
    }
}

return $config;
