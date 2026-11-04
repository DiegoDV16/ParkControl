<?php
/**
 * ==========================================================================
 * ParkControl · app/views/errores/index.php
 * ==========================================================================
 * Página de error para respuestas 403 y 404.
 * Variables: $codigo, $titulo, $mensaje.
 * ==========================================================================
 */
$codigo = $codigo ?? 404;
$titulo = $titulo ?? 'Error';
$mensaje = $mensaje ?? 'Ocurrió un problema.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($titulo); ?> · ParkControl</title>
    <link href="<?php echo e(asset('vendor/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('vendor/bootstrap-icons/bootstrap-icons.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/styles.css')); ?>" rel="stylesheet">
</head>
<body class="pc-login d-flex align-items-center min-vh-100">
    <div class="container py-5">
        <div class="card pc-card mx-auto text-center p-4 p-md-5" style="max-width:520px;">
            <div class="pc-login-icono mx-auto mb-3">
                <i class="bi bi-<?php echo $codigo === 404 ? 'compass' : 'shield-lock'; ?>"></i>
            </div>
            <div class="display-5 fw-bold"><?php echo (int) $codigo; ?></div>
            <h1 class="h4 fw-semibold mt-2 mb-2"><?php echo e($titulo); ?></h1>
            <p class="text-muted mb-4"><?php echo e($mensaje); ?></p>
            <a href="<?php echo e(url('dashboard')); ?>" class="btn btn-primary mx-auto">
                <i class="bi bi-house-door me-1"></i> Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>
