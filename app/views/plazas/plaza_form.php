<?php
/**
 * ==========================================================================
 * ParkControl · app/views/plazas/plaza_form.php
 * ==========================================================================
 * Alta manual de una plaza en una fila existente.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Nueva plaza';
$seccion = $seccion ?? 'plazas';
require PC_APP . '/views/layout/header.php';

$errores = $errores ?? [];
$old = $old ?? [];
?>

<div class="mb-4">
    <a href="<?php echo e(url('plazas')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver a plazas
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0">Nueva plaza</h2>
</div>

<div class="card pc-card pc-form-card">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo e(url('plazas/crear')); ?>">
            <?php echo Csrf::campo(); ?>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="idFila" class="form-label">Fila <span class="text-danger">*</span></label>
                    <select class="form-select <?php echo isset($errores['idFila']) ? 'is-invalid' : ''; ?>"
                            id="idFila" name="idFila" required>
                        <option value="">Selecciona una fila</option>
                        <?php foreach ($filas as $f): ?>
                            <option value="<?php echo (int) $f['idFila']; ?>"
                                <?php echo (int) ($old['idFila'] ?? 0) === (int) $f['idFila'] ? 'selected' : ''; ?>>
                                <?php echo e($f['fila'] . ' · ' . $f['capacidadTotal'] . ' plazas'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['idFila'])): ?><div class="invalid-feedback"><?php echo e($errores['idFila']); ?></div><?php endif; ?>
                </div>
                <div class="col-12 col-md-6">
                    <label for="numero" class="form-label">Número / código <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase <?php echo isset($errores['numero']) ? 'is-invalid' : ''; ?>"
                           id="numero" name="numero" maxlength="10" placeholder="D21" required
                           value="<?php echo e($old['numero'] ?? ''); ?>">
                    <?php if (isset($errores['numero'])): ?><div class="invalid-feedback"><?php echo e($errores['numero']); ?></div><?php endif; ?>
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Crear plaza</button>
                <a href="<?php echo e(url('plazas')); ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
