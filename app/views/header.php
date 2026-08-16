<?php

// Título de la sección (topbar + pestaña del navegador). Por defecto: Inicio.
$titulo  = $titulo  ?? 'Inicio';
// Sección activa del menú lateral. Por defecto: inicio.
$seccion = $seccion ?? 'inicio';

// Marca como "activo" el ítem del menú que coincida con la sección actual.
if (!function_exists('pcNavActivo')) {
    function pcNavActivo($seccionActual, $seccionItem) {
        return $seccionActual === $seccionItem ? 'activo' : '';
    }
}

$documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? getcwd()), '/');
$dirApp       = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/');
$urlApp       = str_replace($documentRoot, '', $dirApp);
if ($urlApp === $dirApp) {
    $urlApp = '/ParkControl/app'; // respaldo por si la ruta no coincide
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ParkControl - Sistema de control de estacionamiento">
    <title><?php echo htmlspecialchars($titulo); ?> · ParkControl</title>

    <!-- Bootstrap 5.3 (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (iconografía) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos propios del sistema -->
    <link href="<?php echo $urlApp; ?>/assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="pc-app">
       
        <aside class="pc-sidebar" aria-label="Navegación principal">
            <!-- Cabecera: logo + nombre del sistema -->
            <div class="pc-sidebar-head d-flex align-items-center gap-2">
                <img src="<?php echo $urlApp; ?>/assets/img/logo.svg" alt="Logo de ParkControl" class="pc-logo">
                <div>
                    <div class="pc-marca-titulo">ParkControl</div>
                    <div class="pc-marca-sub">Sistema de estacionamiento</div>
                </div>
            </div>

            <nav class="pc-nav">
                <li><a href="<?php echo $urlApp; ?>/index.php" class="pc-nav-link <?php echo pcNavActivo($seccion, 'inicio'); ?>" aria-current="page"><i class="bi bi-speedometer2"></i> Inicio</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'vehiculos'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-car-front"></i> Vehículos</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'plazas'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-grid-3x3-gap"></i> Plazas</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'clientes'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-people"></i> Clientes</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'tarifas'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-tags"></i> Tarifas</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'reportes'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'configuracion'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-gear"></i> Configuración</a></li>
            </nav>

            <!-- Pie del sidebar: usuario en sesión + cerrar sesión -->
            <div class="pc-sidebar-foot d-flex align-items-center gap-2">
                <span class="pc-avatar">AD</span>
                <div class="lh-1">
                    <div class="fw-semibold" style="color:#fff;">Admin Demo</div>
                    <div class="small opacity-75">Administrador</div>
                </div>
                <a href="<?php echo $urlApp; ?>/views/login.php" class="ms-auto text-decoration-none opacity-75" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right" style="color:#fff;"></i>
                </a>
            </div>
        </aside>

        <div class="offcanvas offcanvas-start pc-sidebar-offcanvas" tabindex="-1" id="pcMenuMovil" aria-labelledby="pcMenuMovilTitulo">
            <div class="offcanvas-header pc-sidebar-head d-flex align-items-center gap-2">
                <img src="<?php echo $urlApp; ?>/assets/img/logo.svg" alt="Logo de ParkControl" class="pc-logo">
                <div id="pcMenuMovilTitulo">
                    <div class="pc-marca-titulo">ParkControl</div>
                    <div class="pc-marca-sub">Sistema de estacionamiento</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" aria-label="Cerrar menú"></button>
            </div>

            <!-- Mismo menú que el sidebar fijo (mantener sincronizado) -->
            <nav class="pc-nav">
                <li><a href="<?php echo $urlApp; ?>/index.php" class="pc-nav-link <?php echo pcNavActivo($seccion, 'inicio'); ?>" aria-current="page"><i class="bi bi-speedometer2"></i> Inicio</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'vehiculos'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-car-front"></i> Vehículos</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'plazas'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-grid-3x3-gap"></i> Plazas</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'clientes'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-people"></i> Clientes</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'tarifas'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-tags"></i> Tarifas</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'reportes'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-file-earmark-bar-graph"></i> Reportes</a></li>
                <li><a href="#" class="pc-nav-link deshabilitado <?php echo pcNavActivo($seccion, 'configuracion'); ?>" tabindex="-1" aria-disabled="true"><i class="bi bi-gear"></i> Configuración</a></li>
            </nav>

            <div class="pc-sidebar-foot d-flex align-items-center gap-2 mt-auto">
                <span class="pc-avatar">AD</span>
                <div class="lh-1">
                    <div class="fw-semibold" style="color:#fff;">Admin Demo</div>
                    <div class="small opacity-75">Administrador</div>
                </div>
                <a href="<?php echo $urlApp; ?>/views/login.php" class="ms-auto text-decoration-none opacity-75" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right" style="color:#fff;"></i>
                </a>
            </div>
        </div>

        <div class="pc-app-main">
            <!-- Topbar: menú móvil, título de sección, buscador y usuario -->
            <header class="pc-topbar d-flex align-items-center gap-3">
                <!-- Botón que abre el menú en móvil (oculto en desktop) -->
                <button class="btn btn-outline-secondary d-lg-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#pcMenuMovil"
                        aria-controls="pcMenuMovil" aria-label="Abrir menú">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Título de la sección actual (variable $titulo) -->
                <h1 class="h5 fw-bold mb-0 d-none d-md-block"><?php echo htmlspecialchars($titulo); ?></h1>

                <!-- Buscador global (maqueta, sin funcionalidad aún) -->
                <div class="flex-grow-1 mx-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control bg-transparent border-start-0"
                               placeholder="Buscar..." aria-label="Buscar">
                    </div>
                </div>

                <!-- Notificaciones con contador (maqueta) -->
                <button class="btn position-relative text-muted" type="button" aria-label="Notificaciones">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">3</span>
                </button>

                <!-- Usuario en sesión -->
                <span class="pc-avatar" title="Admin Demo">AD</span>
            </header>
            <main class="pc-contenido">
