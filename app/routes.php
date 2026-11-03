<?php
/**
 * ==========================================================================
 * ParkControl · app/routes.php
 * ==========================================================================
 * Tabla de rutas del front controller.
 * ==========================================================================
 */

return static function (Router $router): void {
    // Autenticación
    $router->get('login', [AuthController::class, 'mostrarLogin'], ['auth' => false]);
    $router->post('login', [AuthController::class, 'ingresar'], ['auth' => false]);
    $router->get('logout', [AuthController::class, 'salir']);

    // Panel
    $router->get('dashboard', [DashboardController::class, 'index']);
};
