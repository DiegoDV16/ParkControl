<?php
/**
 * ==========================================================================
 * ParkControl · app/views/plazas/fila_form.php
 * ==========================================================================
 * Alta de una fila/sector con generación automática de plazas.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Nueva fila';
$seccion = $seccion ?? 'plazas';
require PC_APP . '/views/layout/header.php';

$errores = $errores ?? [];
$old = $old ?? [];
?>

<div class="mb-4">
    <a href="<?php echo e(url('plazas')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver a plazas
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0">Nueva fila</h2>
    <p class="text-muted small mb-0">Se crearán automáticamente las plazas numeradas de la fila.</p>
</div>

<div class="card pc-card pc-form-card">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo e(url('plazas/filas/crear')); ?>">
            <?php echo Csrf::campo(); ?>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="fila" class="form-label">Nombre de la fila <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase <?php echo isset($errores['fila']) ? 'is-invalid' : ''; ?>"
                           id="fila" name="fila" maxlength="12" placeholder="D" required
                           value="<?php echo e($old['fila'] ?? ''); ?>">
                    <?php if (isset($errores['fila'])): ?><div class="invalid-feedback"><?php echo e($errores['fila']); ?></div><?php endif; ?>
                </div>
                <div class="col-12 col-md-6">
                    <label for="capacidadTotal" class="form-label">Capacidad <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?php echo isset($errores['capacidadTotal']) ? 'is-invalid' : ''; ?>"
                           id="capacidadTotal" name="capacidadTotal" min="1" max="200" required
                           value="<?php echo e($old['capacidadTotal'] ?? 20); ?>">
                    <?php if (isset($errores['capacidadTotal'])): ?><div class="invalid-feedback"><?php echo e($errores['capacidadTotal']); ?></div><?php endif; ?>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Crear fila</button>
                <a href="<?php echo e(url('plazas')); ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
