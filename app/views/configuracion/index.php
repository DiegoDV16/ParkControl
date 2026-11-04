<?php
/**
 * ==========================================================================
 * ParkControl · app/views/configuracion/index.php
 * ==========================================================================
 * Parámetros del estacionamiento y cambio de contraseña.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Configuración';
$seccion = $seccion ?? 'configuracion';
require PC_APP . '/views/layout/header.php';

$errores = $errores ?? [];
$old = $old ?? [];
$usuario = Auth::usuario();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Configuración</h2>
        <p class="text-muted mb-0 small">Datos del estacionamiento y seguridad de tu cuenta.</p>
    </div>
</div>

<?php if ($errores !== []): ?>
    <div class="alert alert-danger small">Revisa los campos marcados.</div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card pc-card">
            <div class="card-body p-4">
                <h3 class="h6 fw-bold mb-3">Datos del estacionamiento</h3>
                <form method="POST" action="<?php echo e(url('configuracion')); ?>" novalidate>
                    <?php echo Csrf::campo(); ?>

                    <div class="mb-3">
                        <label for="nombre_estacionamiento" class="form-label">Nombre</label>
                        <input type="text" class="form-control <?php echo isset($errores['nombre_estacionamiento']) ? 'is-invalid' : ''; ?>"
                               id="nombre_estacionamiento" name="nombre_estacionamiento" maxlength="100" required
                               value="<?php echo e($old['nombre_estacionamiento'] ?? ($valores['nombre_estacionamiento'] ?? 'ParkControl')); ?>">
                        <?php if (isset($errores['nombre_estacionamiento'])): ?><div class="invalid-feedback"><?php echo e($errores['nombre_estacionamiento']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" maxlength="150"
                               value="<?php echo e($old['direccion'] ?? ($valores['direccion'] ?? '')); ?>">
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" maxlength="30"
                                   value="<?php echo e($old['telefono'] ?? ($valores['telefono'] ?? '')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="moneda" class="form-label">Moneda</label>
                            <select class="form-select" id="moneda" name="moneda">
                                <?php foreach (['CLP', 'USD', 'EUR'] as $moneda): ?>
                                    <option value="<?php echo $moneda; ?>"
                                        <?php echo ($old['moneda'] ?? ($valores['moneda'] ?? 'CLP')) === $moneda ? 'selected' : ''; ?>>
                                        <?php echo $moneda; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar configuración
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card pc-card mb-3">
            <div class="card-body p-4">
                <h3 class="h6 fw-bold mb-3">Cambiar contraseña</h3>
                <form method="POST" action="<?php echo e(url('configuracion/clave')); ?>" novalidate>
                    <?php echo Csrf::campo(); ?>

                    <div class="mb-3">
                        <label for="clave_actual" class="form-label">Contraseña actual</label>
                        <input type="password" class="form-control" id="clave_actual" name="clave_actual"
                               autocomplete="current-password" required>
                    </div>
                    <div class="mb-3">
                        <label for="clave_nueva" class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" id="clave_nueva" name="clave_nueva"
                               minlength="8" autocomplete="new-password" required>
                        <div class="form-text">Mínimo 8 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="clave_confirmar" class="form-label">Confirmar nueva contraseña</label>
                        <input type="password" class="form-control" id="clave_confirmar" name="clave_confirmar"
                               minlength="8" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-shield-lock me-1"></i> Actualizar contraseña
                    </button>
                </form>
            </div>
        </div>

        <div class="card pc-card">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3">Información del sistema</h3>
                <ul class="list-unstyled small mb-0">
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Versión</span>
                        <strong><?php echo e(config('app.version', '1.0.0')); ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">PHP</span>
                        <strong><?php echo e(PHP_VERSION); ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Base de datos</span>
                        <strong><?php echo e(config('db.nombre', 'parkcontrol')); ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Usuario</span>
                        <strong><?php echo e($usuario['correo'] ?? ''); ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
