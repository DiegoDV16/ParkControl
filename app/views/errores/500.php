<?php
/**
 * ==========================================================================
 * ParkControl · app/views/errores/500.php
 * ==========================================================================
 * Error interno. En producción no muestra detalles técnicos.
 * Variable: $mensaje (solo en modo depuración).
 * ==========================================================================
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error interno · ParkControl</title>
    <link href="<?php echo e(asset('vendor/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/styles.css')); ?>" rel="stylesheet">
</head>
<body class="pc-login d-flex align-items-center min-vh-100">
    <div class="container py-5">
        <div class="card pc-card mx-auto p-4 p-md-5" style="max-width:640px;">
            <h1 class="h4 fw-bold mb-2">No pudimos completar la operación</h1>
            <p class="text-muted">
                Ocurrió un error interno. Si el problema persiste, contacta al administrador
                del sistema.
            </p>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-warning small mt-3 mb-0">
                    <strong>Detalle (modo depuración):</strong><br>
                    <?php echo e($mensaje); ?>
                </div>
            <?php endif; ?>
            <a href="<?php echo e(url('dashboard')); ?>" class="btn btn-primary mt-4">
                Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>
