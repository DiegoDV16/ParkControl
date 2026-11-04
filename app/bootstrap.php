<?php
/**
 * ==========================================================================
 * ParkControl · app/bootstrap.php
 * ==========================================================================
 * Arranque común de la aplicación: configuración, zona horaria, sesión segura,
 * autocarga de clases y helpers.
 * ==========================================================================
 */

declare(strict_types=1);

define('PC_APP', __DIR__);
define('PC_RAIZ', dirname(__DIR__));

$config = require PC_APP . '/config/config.php';
$GLOBALS['pc_config'] = $config;

require_once PC_APP . '/config/conexion.php';

date_default_timezone_set($config['app']['zona_horaria']);
mb_internal_encoding('UTF-8');

error_reporting(E_ALL);
ini_set('display_errors', $config['app']['depuracion'] ? '1' : '0');
ini_set('log_errors', '1');

// Ruta pública de la app: /ParkControl/app
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/ParkControl/app/index.php');
$base = rtrim(dirname($script), '/');
define('BASE_URL', $base === '.' || $base === '\\' ? '' : $base);

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('PCSESSID');
    session_start();
}

// Autocarga simple: controladores, modelos y núcleo (sin namespaces).
spl_autoload_register(static function (string $clase): void {
    $candidatos = [
        PC_APP . '/controllers/' . $clase . '.php',
        PC_APP . '/models/' . $clase . '.php',
        PC_APP . '/core/' . $clase . '.php',
    ];
    foreach ($candidatos as $archivo) {
        if (is_file($archivo)) {
            require_once $archivo;
            return;
        }
    }
});

require_once PC_APP . '/core/helpers.php';

// Credenciales mal configuradas: mensaje claro en lugar de un error crudo.
if (!is_file(PC_APP . '/config/config.local.php')) {
    http_response_code(500);
    echo '<h1>ParkControl no está configurado</h1>';
    echo '<p>Falta <code>app/config/config.local.php</code> con las credenciales de MySQL. ';
    echo 'Copia <code>config.local.example.php</code> y ajusta los valores.</p>';
    exit;
}
