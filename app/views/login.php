<?php

require_once __DIR__ . "/../config/conexion.php";
session_start();

// Si ya hay sesión iniciada, ir directo al panel
if (isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo     = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($correo === '' || $contrasena === '') {
        $error = "Ingresa tu correo y tu contraseña.";
    } else {
        $conn = Database::getConexion();
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = :correo LIMIT 1");
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $hash = $usuario['contrasena'];
            $valida = password_verify($contrasena, $hash);

            // Fallback: si el registro aún está en texto plano, comparar directo
            // y migrar a hash para no dejar contraseñas sin cifrar.
            if (!$valida && hash_equals($hash, $contrasena)) {
                $valida = true;
                $nuevoHash = password_hash($contrasena, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE usuario SET contrasena = :hash WHERE idUsuario = :id");
                $upd->execute([':hash' => $nuevoHash, ':id' => $usuario['idUsuario']]);
            }

            if ($valida) {
                $_SESSION['usuario'] = [
                    'id'      => $usuario['idUsuario'],
                    'rut'     => $usuario['rut'],
                    'nombres' => $usuario['nombres'],
                    'correo'  => $usuario['correo'],
                    'rol'     => $usuario['idRol'],
                ];
                header("Location: ../index.php");
                exit;
            }
        }

        // Mismo mensaje para usuario inexistente o contraseña mala (no filtrar cuál falló)
        $error = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Iniciar sesión en ParkControl">
    <title>Iniciar sesión · ParkControl</title>

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

                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li class="pc-login-caracteristica">
                        <i class="bi bi-grid-3x3-gap"></i>
                        <span>Mapa visual de plazas disponibles y ocupadas</span>
                    </li>
                    <li class="pc-login-caracteristica">
                        <i class="bi bi-clock-history"></i>
                        <span>Registro de entradas y salidas de vehículos</span>
                    </li>
                    <li class="pc-login-caracteristica">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Reportes e ingresos diarios del estacionamiento</span>
                    </li>
                </ul>
            </div>
        </section>
        <section class="col-lg-6 pc-login-col p-4 p-md-5">
            <div class="card pc-login-card mx-auto">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4 d-lg-none">
                        <img src="../assets/img/logo.svg" alt="Logo de ParkControl" class="mb-2" style="width:56px; height:56px;">
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2" role="alert">
                            <i class="bi bi-exclamation-triangle me-1"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- formulario de inicio de sesion -->
                    <form action="" method="POST">
                        <!-- Campo: usuario -->
                        <div class="mb-3">
                            <label for="loginUsuario" class="form-label">Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="loginUsuario" placeholder="Ingresa tu correo" name="usuario" value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" required>
                            </div>
                        </div>
                        <!-- Campo: contraseña -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="loginPassword" class="form-label">Contraseña</label>
                                <!-- Enlace de recuperación (futuro) -->
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control" id="loginPassword" placeholder="Ingresa tu contraseña" name="contrasena" required>
                            </div>
                            <a href="recuperarContrasena.php" class="small text-decoration-none text-right">¿Olvidaste tu contraseña?</a>
                        </div>

                        <!-- Recordarme -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="loginRecordar">
                            <label class="form-check-label" for="loginRecordar">Recordarme en este equipo</label>
                        </div>

                        <!-- Botón de acceso -->
                        <button type="submit" class="btn btn-ingresar w-100 d-inline-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </button>
                    </form>

                    <!-- Pie del formulario -->
                    <p class="text-center text-muted small mt-4 mb-0">
                        © 2026 ParkControl · v1.07.08.26
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Bootstrap 5.3 (JavaScript bundle) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
