<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Recuperar contraseña de ParkControl">
    <title>Recuperar contraseña · ParkControl</title>

    <!-- Bootstrap 5.3 (CSS) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (iconografía) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos propios del sistema (ruta relativa hacia app/assets/) -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <main class="pc-login row g-0">
        
        <section class="col-lg-6 d-none d-lg-flex pc-login-marca pc-login-col p-5">
            <div class="mx-auto" style="max-width: 420px;">
                <!-- Logo del sistema -->
                <img src="../assets/img/logo.svg" alt="Logo de ParkControl" class="pc-logo mb-4">

                <!-- Insignia del sistema -->
                <span class="pc-login-badge mb-4"><i class="bi bi-car-front"></i> Sistema de estacionamiento</span>

                <!-- Nombre del sistema -->
                <h1 class="display-5 fw-bold mb-3">ParkControl</h1>

                <!-- Descripción breve -->
                <p class="fs-5 opacity-75 mb-5">
                    Control de estacionamiento inteligente: plazas, vehículos
                    y reportes en tiempo real, todo en un solo lugar.
                </p>

                <!-- Beneficios clave con iconos -->
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li class="pc-login-caracteristica">
                        <i class="bi bi-shield-check"></i>
                        <span>Acceso seguro con credenciales protegidas</span>
                    </li>
                    <li class="pc-login-caracteristica">
                        <i class="bi bi-envelope-arrow-up"></i>
                        <span>Recupera tu acceso por correo electrónico</span>
                    </li>
                    <li class="pc-login-caracteristica">
                        <i class="bi bi-clock-history"></i>
                        <span>Registro de entradas y salidas de vehículos</span>
                    </li>
                </ul>
            </div>
        </section>

        <section class="col-lg-6 pc-login-col p-4 p-md-5">
            <div class="card pc-login-card mx-auto">
                <div class="card-body p-4 p-md-5">

                    <!-- Logo pequeño (visible solo en móvil, cuando el
                         panel de marca está oculto) -->
                    <div class="text-center mb-4 d-lg-none">
                        <img src="../assets/img/logo.svg" alt="Logo de ParkControl" class="mb-2" style="width:56px; height:56px;">
                    </div>

                    <!-- Encabezado del formulario -->
                    <div class="text-center mb-4">
                        <!-- Icono circular de la sección -->

                        <h2 class="h4 fw-bold mb-1">¿Olvidaste tu contraseña?</h2>
                    </div>

                    <!-- Formulario de recuperación (maqueta, no envía correo) -->
                    <form action="#" method="post" novalidate>
                        <!-- Campo: correo -->
                        <div class="mb-4">
                            <label for="recEmail" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="recEmail"
                                       placeholder="usuario@correo.com" autocomplete="email"
                                       aria-describedby="recEmailAyuda" required>
                            </div>
                        </div>

                        <!-- Botón para enviar el enlace -->
                        <a href="#" class="btn btn-ingresar w-100 d-inline-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-send"></i> Enviar enlace de recuperación
                        </a>
                    </form>

                    <!-- Enlace de regreso al login -->
                    <p class="text-center mt-4 mb-0">
                        <a href="login.php" class="text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión
                        </a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Bootstrap 5.3 (JavaScript bundle) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
