<?php
/**
 * ==========================================================================
 * ParkControl · app/views/layout/header.php
 * ==========================================================================
 * Cabecera compartida: <head>, sidebar, menú móvil y topbar.
 * Variables esperadas: $titulo, $seccion.
 * ==========================================================================
 */

$titulo  = $titulo  ?? 'Inicio';
$seccion = $seccion ?? 'dashboard';
$usuario = Auth::usuario();
$rol     = (int) ($usuario['rol'] ?? 0);
$nombre  = nombreCompleto($usuario ?? []);
$avatar  = iniciales($nombre);
$rolTexto = $rol === 1 ? 'Administrador' : 'Operador';

/**
 * Menú lateral. `pronto` marca los módulos que aún no están disponibles.
 * `roles` limita la visibilidad según el rol del usuario.
 */
$menu = [
    ['ruta' => 'dashboard', 'texto' => 'Inicio',        'icono' => 'speedometer2',        'rol' => [1, 2], 'pronto' => false],
    ['ruta' => 'ingresos',  'texto' => 'Ingresos',      'icono' => 'car-front',           'rol' => [1, 2], 'pronto' => false],
    ['ruta' => 'plazas',    'texto' => 'Plazas',        'icono' => 'grid-3x3-gap',        'rol' => [1, 2], 'pronto' => false],
    ['ruta' => 'clientes',  'texto' => 'Clientes',      'icono' => 'people',              'rol' => [1, 2], 'pronto' => false],
    ['ruta' => 'vehiculos', 'texto' => 'Vehículos',     'icono' => 'car-front',           'rol' => [1, 2], 'pronto' => false],
    ['ruta' => 'tarifas',   'texto' => 'Tarifas',       'icono' => 'tags',                'rol' => [1],    'pronto' => false],
    ['ruta' => 'usuarios',  'texto' => 'Usuarios',      'icono' => 'person-gear',         'rol' => [1],    'pronto' => false],
    ['ruta' => 'reportes',  'texto' => 'Reportes',      'icono' => 'file-earmark-bar-graph', 'rol' => [1], 'pronto' => false],
    ['ruta' => 'configuracion', 'texto' => 'Configuración', 'icono' => 'gear',           'rol' => [1],    'pronto' => false],
];

/** Pinta el menú (dos veces: sidebar fijo y offcanvas móvil). */
$pintarMenu = static function () use ($menu, $rol, $seccion): void {
    foreach ($menu as $item) {
        if (!in_array($rol, $item['rol'], true)) {
            continue;
        }
        $activo = $seccion === $item['ruta'] ? 'activo' : '';
        if ($item['pronto']) {
            echo '<li><a href="#" class="pc-nav-link deshabilitado ' . $activo . '" tabindex="-1" aria-disabled="true" title="Disponible próximamente">'
                . '<i class="bi bi-' . e($item['icono']) . '"></i> ' . e($item['texto'])
                . ' <span class="pc-badge text-bg-light ms-auto" style="font-size:.6rem;">Pronto</span></a></li>';
            continue;
        }
        echo '<li><a href="' . e(url($item['ruta'])) . '" class="pc-nav-link ' . $activo . '">'
            . '<i class="bi bi-' . e($item['icono']) . '"></i> ' . e($item['texto']) . '</a></li>';
    }
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ParkControl - Sistema de control de estacionamiento">
    <title><?php echo e($titulo); ?> · <?php echo e(config('app.nombre', 'ParkControl')); ?></title>

    <link href="<?php echo e(asset('vendor/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('vendor/bootstrap-icons/bootstrap-icons.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/styles.css')); ?>" rel="stylesheet">
</head>

<body<?php echo !empty($abrirModal) ? ' data-abrir-modal="' . e($abrirModal) . '"' : ''; ?>>
    <div class="pc-app">

        <aside class="pc-sidebar" aria-label="Navegación principal">
            <div class="pc-sidebar-head d-flex align-items-center gap-2">
                <img src="<?php echo e(asset('img/logo.svg')); ?>" alt="Logo de ParkControl" class="pc-logo">
                <div>
                    <div class="pc-marca-titulo">ParkControl</div>
                    <div class="pc-marca-sub">Sistema de estacionamiento</div>
                </div>
            </div>

            <nav class="pc-nav" aria-label="Secciones">
                <?php $pintarMenu(); ?>
            </nav>

            <div class="pc-sidebar-foot d-flex align-items-center gap-2">
                <span class="pc-avatar"><?php echo e($avatar); ?></span>
                <div class="lh-1">
                    <div class="fw-semibold" style="color:#fff;"><?php echo e($nombre); ?></div>
                    <div class="small opacity-75"><?php echo e($rolTexto); ?></div>
                </div>
                <a href="<?php echo e(url('logout')); ?>" class="ms-auto text-decoration-none opacity-75" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right" style="color:#fff;"></i>
                </a>
            </div>
        </aside>

        <div class="offcanvas offcanvas-start pc-sidebar-offcanvas" tabindex="-1" id="pcMenuMovil" aria-labelledby="pcMenuMovilTitulo">
            <div class="offcanvas-header pc-sidebar-head d-flex align-items-center gap-2">
                <img src="<?php echo e(asset('img/logo.svg')); ?>" alt="Logo de ParkControl" class="pc-logo">
                <div id="pcMenuMovilTitulo">
                    <div class="pc-marca-titulo">ParkControl</div>
                    <div class="pc-marca-sub">Sistema de estacionamiento</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Cerrar menú"></button>
            </div>

            <nav class="pc-nav" aria-label="Secciones (móvil)">
                <?php $pintarMenu(); ?>
            </nav>

            <div class="pc-sidebar-foot d-flex align-items-center gap-2 mt-auto">
                <span class="pc-avatar"><?php echo e($avatar); ?></span>
                <div class="lh-1">
                    <div class="fw-semibold" style="color:#fff;"><?php echo e($nombre); ?></div>
                    <div class="small opacity-75"><?php echo e($rolTexto); ?></div>
                </div>
                <a href="<?php echo e(url('logout')); ?>" class="ms-auto text-decoration-none opacity-75" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right" style="color:#fff;"></i>
                </a>
            </div>
        </div>

        <div class="pc-app-main">
            <header class="pc-topbar d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#pcMenuMovil"
                        aria-controls="pcMenuMovil" aria-label="Abrir menú">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <h1 class="h5 fw-bold mb-0"><?php echo e($titulo); ?></h1>

                <span class="ms-auto small text-muted d-none d-md-inline">
                    <i class="bi bi-calendar3 me-1"></i><?php echo e(strftime_es()); ?>
                </span>

                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="pc-avatar"><?php echo e($avatar); ?></span>
                        <span class="d-none d-sm-inline small fw-semibold"><?php echo e($usuario['nombres'] ?? ''); ?></span>
                        <i class="bi bi-chevron-down small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li class="px-3 py-2">
                            <div class="fw-semibold"><?php echo e($nombre); ?></div>
                            <div class="small text-muted"><?php echo e($usuario['correo'] ?? ''); ?></div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo e(url('logout')); ?>">
                            <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                        </a></li>
                    </ul>
                </div>
            </header>

            <main class="pc-contenido">
                <?php foreach (Flash::tomar() as $mensaje): ?>
                    <div class="alert alert-<?php echo e($mensaje['tipo']); ?> alert-dismissible fade show" role="alert">
                        <?php echo e($mensaje['mensaje']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endforeach; ?>
