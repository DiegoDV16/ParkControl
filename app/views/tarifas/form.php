<?php
/**
 * ==========================================================================
 * ParkControl · app/views/tarifas/form.php
 * ==========================================================================
 * Alta y edición de tarifas.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Tarifa';
$seccion = $seccion ?? 'tarifas';
require PC_APP . '/views/layout/header.php';

$esEdicion = $modo === 'editar';
$valor = static function (string $campo, mixed $defecto = '') use ($old, $tarifa): mixed {
    return $old[$campo] ?? $tarifa[$campo] ?? $defecto;
};
$accion = $esEdicion ? url('tarifas/actualizar') : url('tarifas/crear');
$fracciones = [5, 10, 15, 20, 30, 60, 90, 120];
?>

<div class="mb-4">
    <a href="<?php echo e(url('tarifas')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver a tarifas
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0"><?php echo $esEdicion ? 'Editar tarifa' : 'Nueva tarifa'; ?></h2>
</div>

<div class="card pc-card pc-form-card">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo e($accion); ?>" novalidate>
            <?php echo Csrf::campo(); ?>
            <?php if ($esEdicion): ?>
                <input type="hidden" name="idTarifa" value="<?php echo (int) ($tarifa['idTarifa'] ?? 0); ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errores['nombre']) ? 'is-invalid' : ''; ?>"
                           id="nombre" name="nombre" maxlength="50" placeholder="Tarifa general" required
                           value="<?php echo e($valor('nombre')); ?>">
                    <?php if (isset($errores['nombre'])): ?><div class="invalid-feedback"><?php echo e($errores['nombre']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="valorHora" class="form-label">Valor por hora (CLP) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?php echo isset($errores['valorHora']) ? 'is-invalid' : ''; ?>"
                           id="valorHora" name="valorHora" min="1" step="50" required
                           value="<?php echo e($valor('valorHora')); ?>">
                    <?php if (isset($errores['valorHora'])): ?><div class="invalid-feedback"><?php echo e($errores['valorHora']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="fraccionMinutos" class="form-label">Fracción de cobro <span class="text-danger">*</span></label>
                    <select class="form-select" id="fraccionMinutos" name="fraccionMinutos">
                        <?php foreach ($fracciones as $minutos): ?>
                            <option value="<?php echo $minutos; ?>"
                                <?php echo (int) $valor('fraccionMinutos', 60) === $minutos ? 'selected' : ''; ?>>
                                Cada <?php echo $minutos; ?> minutos
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="valorFraccion" class="form-label">Valor por fracción (opcional)</label>
                    <input type="number" class="form-control" id="valorFraccion" name="valorFraccion"
                           min="0" step="10" value="<?php echo e($valor('valorFraccion')); ?>">
                    <div class="form-text">Si se deja vacío, se prorratea el valor hora.</div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?php echo $esEdicion ? 'Guardar cambios' : 'Crear tarifa'; ?>
                </button>
                <a href="<?php echo e(url('tarifas')); ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
