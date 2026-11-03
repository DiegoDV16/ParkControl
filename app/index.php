<?php
/**
 * ==========================================================================
 * ParkControl · app/index.php
 * ==========================================================================
 * Front controller: único punto de entrada de la aplicación.
 *
 *   /ParkControl/app/index.php                 → panel
 *   /ParkControl/app/index.php?ruta=login      → inicio de sesión
 * ==========================================================================
 */

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

set_exception_handler(static function (Throwable $error): void {
    error_log(sprintf(
        '[ParkControl] %s en %s:%d',
        $error->getMessage(),
        $error->getFile(),
        $error->getLine()
    ));

    http_response_code(500);
    $mensaje = config('app.depuracion') ? $error->getMessage() : null;
    require PC_APP . '/views/errores/500.php';
});

$router = new Router();
(require __DIR__ . '/routes.php')($router);
$router->despachar();
