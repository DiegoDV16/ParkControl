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
    $router->get('clientes', [ClienteController::class, 'index']);    $router->get('clientes/crear', [ClienteController::class, 'crear']);
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

    // Plazas y filas
    $router->get('plazas', [PlazaController::class, 'index']);
    $router->get('plazas/crear', [PlazaController::class, 'crear']);
    $router->post('plazas/crear', [PlazaController::class, 'guardar']);
    $router->post('plazas/estado', [PlazaController::class, 'estado']);
    $router->get('plazas/filas/crear', [PlazaController::class, 'crearFila']);
    $router->post('plazas/filas/crear', [PlazaController::class, 'guardarFila']);

    // Tarifas (solo administración)
    $router->get('tarifas', [TarifaController::class, 'index'], ['rol' => 1]);
    $router->get('tarifas/crear', [TarifaController::class, 'crear'], ['rol' => 1]);
    $router->post('tarifas/crear', [TarifaController::class, 'guardar'], ['rol' => 1]);
    $router->get('tarifas/editar', [TarifaController::class, 'editar'], ['rol' => 1]);
    $router->post('tarifas/actualizar', [TarifaController::class, 'actualizar'], ['rol' => 1]);
    $router->post('tarifas/estado', [TarifaController::class, 'estado'], ['rol' => 1]);

    // Ingresos y cobros
    $router->get('ingresos', [IngresoController::class, 'index']);
    $router->get('ingresos/crear', [IngresoController::class, 'crear']);
    $router->post('ingresos/crear', [IngresoController::class, 'guardar']);
    $router->get('ingresos/salida', [IngresoController::class, 'salida']);
    $router->post('ingresos/cerrar', [IngresoController::class, 'cerrar']);
    $router->post('ingresos/anular', [IngresoController::class, 'anular']);
};
