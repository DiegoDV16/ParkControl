<?php
/**
 * ==========================================================================
 * ParkControl · app/views/auth/login.php
 * ==========================================================================
 * Pantalla de inicio de sesión (página completa, sin layout lateral).
 * ==========================================================================
 */

$mensajes = Flash::tomar();
$correo = (string) antiguo('correo', '');
$nombreApp = config('app.nombre', 'ParkControl');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Iniciar sesión en ParkControl">
    <title>Iniciar sesión · <?php echo e($nombreApp); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/styles.css')); ?>" rel="stylesheet">
</head>

<body class="pc-login">
    <div class="container-fluid">
        <div class="row g-0 min-vh-100">

            <!-- Panel de marca -->
            <section class="col-lg-6 d-none d-lg-flex pc-login-marca flex-column justify-content-between p-5">
                <div class="d-flex align-items-center gap-3">
                    <img src="<?php echo e(asset('img/logo.svg')); ?>" alt="Logo de ParkControl" class="pc-logo">
                    <div>
                        <div class="fs-4 fw-bold"><?php echo e($nombreApp); ?></div>
                        <div class="small opacity-75">Sistema de control de estacionamiento</div>
                    </div>
                </div>

                <div>
                    <span class="pc-login-badge mb-3">
                        <i class="bi bi-shield-check"></i> Acceso seguro
                    </span>
                    <h1 class="display-6 fw-bold mb-3">Controla tu estacionamiento en tiempo real</h1>
                    <p class="opacity-75 mb-4">
                        Registra ingresos, administra plazas y consulta la recaudación del día
                        desde un solo lugar.
                    </p>

                    <div class="d-flex flex-column gap-2 mt-4">
                        <div class="pc-login-caracteristica">
                            <i class="bi bi-car-front"></i>
                            <div>
                                <div class="fw-semibold small">Ingresos y salidas</div>
                                <div class="small opacity-75">Cobro automático según la tarifa vigente</div>
                            </div>
                        </div>
                        <div class="pc-login-caracteristica">
                            <i class="bi bi-grid-3x3-gap"></i>
                            <div>
                                <div class="fw-semibold small">Ocupación de plazas</div>
                                <div class="small opacity-75">Disponibilidad actualizada al instante</div>
                            </div>
                        </div>
                        <div class="pc-login-caracteristica">
                            <i class="bi bi-graph-up-arrow"></i>
                            <div>
                                <div class="fw-semibold small">Reportes de recaudación</div>
                                <div class="small opacity-75">Historial por período y medio de pago</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="small opacity-50">
                    &copy; <?php echo date('Y'); ?> <?php echo e($nombreApp); ?> · Todos los derechos reservados
                </div>
            </section>

            <!-- Formulario -->
            <section class="col-lg-6 pc-login-col p-4 p-md-5">
                <div class="card pc-login-card mx-auto">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4 d-lg-none">
                            <img src="<?php echo e(asset('img/logo.svg')); ?>" alt="Logo de ParkControl" class="mb-2" style="width:64px;height:64px;">
                            <div class="fw-bold fs-5"><?php echo e($nombreApp); ?></div>
                        </div>

                        <span class="pc-login-badge d-inline-flex mb-3" style="background:#eef4fb;color:#0a6dbd;border-color:#d6e6f7;">
                            <i class="bi bi-lock"></i> Iniciar sesión
                        </span>

                        <h2 class="h3 fw-bold mb-1">Bienvenido</h2>
                        <p class="text-muted small mb-4">Ingresa tus credenciales para continuar.</p>

                        <?php foreach ($mensajes as $mensaje): ?>
                            <div class="alert alert-<?php echo e($mensaje['tipo']); ?> py-2 small" role="alert">
                                <?php echo e($mensaje['mensaje']); ?>
                            </div>
                        <?php endforeach; ?>

                        <form method="POST" action="<?php echo e(url('login')); ?>" novalidate>
                            <?php echo Csrf::campo(); ?>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="correo" name="correo"
                                           value="<?php echo e($correo); ?>" placeholder="usuario@correo.cl"
                                           autocomplete="username" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="clave" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control" id="clave" name="clave"
                                           placeholder="Tu contraseña" autocomplete="current-password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="verClave"
                                            aria-label="Mostrar u ocultar contraseña">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="recordar" disabled>
                                    <label class="form-check-label small text-muted" for="recordar">Recordarme</label>
                                </div>
                                <span class="small text-muted" title="Solicita el cambio al administrador">
                                    ¿Olvidaste tu contraseña?
                                </span>
                            </div>

                            <button type="submit" class="btn btn-ingresar w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                            </button>
                        </form>

                        <p class="text-center text-muted small mt-4 mb-0">
                            ¿Problemas para acceder? Contacta al administrador del sistema.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            var boton = document.getElementById('verClave');
            var campo = document.getElementById('clave');
            if (!boton || !campo) return;
            boton.addEventListener('click', function () {
                var visible = campo.type === 'text';
                campo.type = visible ? 'password' : 'text';
                boton.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        })();
    </script>
</body>
</html>
