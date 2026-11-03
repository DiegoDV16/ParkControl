<?php
/**
 * ==========================================================================
 * ParkControl · app/routes.php
 * ==========================================================================
 * Tabla de rutas del front controller.
 *
 * Opciones por ruta:
 *   'auth' => false   permite el acceso sin sesión (login).
 *   'rol'  => 1       exige un rol concreto (1 = Administrador).
 * ==========================================================================
 */

return static function (Router $router): void {
    // Autenticación
    $router->get('login', [AuthController::class, 'mostrarLogin'], ['auth' => false]);
    $router->post('login', [AuthController::class, 'ingresar'], ['auth' => false]);
    $router->get('logout', [AuthController::class, 'salir']);

    // Panel
    $router->get('dashboard', [DashboardController::class, 'index']);

    // Usuarios (solo administración)
    $router->get('usuarios', [UsuarioController::class, 'index'], ['rol' => 1]);
    $router->get('usuarios/crear', [UsuarioController::class, 'crear'], ['rol' => 1]);
    $router->post('usuarios/crear', [UsuarioController::class, 'guardar'], ['rol' => 1]);
    $router->get('usuarios/editar', [UsuarioController::class, 'editar'], ['rol' => 1]);
    $router->post('usuarios/actualizar', [UsuarioController::class, 'actualizar'], ['rol' => 1]);
    $router->post('usuarios/estado', [UsuarioController::class, 'estado'], ['rol' => 1]);
    $router->post('usuarios/restablecer', [UsuarioController::class, 'restablecer'], ['rol' => 1]);

    // Clientes (administración y operación)
    $router->get('clientes', [ClienteController::class, 'index']);
    $router->get('clientes/crear', [ClienteController::class, 'crear']);
    $router->post('clientes/crear', [ClienteController::class, 'guardar']);
    $router->get('clientes/editar', [ClienteController::class, 'editar']);
    $router->post('clientes/actualizar', [ClienteController::class, 'actualizar']);
    $router->post('clientes/estado', [ClienteController::class, 'estado']);

    // Vehículos
    $router->get('vehiculos', [VehiculoController::class, 'index']);
    $router->get('vehiculos/crear', [VehiculoController::class, 'crear']);
    $router->post('vehiculos/crear', [VehiculoController::class, 'guardar']);
    $router->get('vehiculos/editar', [VehiculoController::class, 'editar']);
    $router->post('vehiculos/actualizar', [VehiculoController::class, 'actualizar']);
    $router->post('vehiculos/eliminar', [VehiculoController::class, 'eliminar']);
};
