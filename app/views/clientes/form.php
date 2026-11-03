<?php
/**
 * ==========================================================================
 * ParkControl · app/views/clientes/form.php
 * ==========================================================================
 * Alta y edición de clientes.
 * Variables: $modo, $cliente, $errores, $old.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Cliente';
$seccion = $seccion ?? 'clientes';
require PC_APP . '/views/layout/header.php';

$esEdicion = $modo === 'editar';
$valor = static function (string $campo, mixed $defecto = '') use ($old, $cliente): mixed {
    return $old[$campo] ?? $cliente[$campo] ?? $defecto;
};
$accion = $esEdicion ? url('clientes/actualizar') : url('clientes/crear');
?>

<div class="mb-4">
    <a href="<?php echo e(url('clientes')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0"><?php echo $esEdicion ? 'Editar cliente' : 'Nuevo cliente'; ?></h2>
</div>

<div class="card pc-card pc-form-card">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo e($accion); ?>" novalidate>
            <?php echo Csrf::campo(); ?>
            <?php if ($esEdicion): ?>
                <input type="hidden" name="idCliente" value="<?php echo (int) ($cliente['idCliente'] ?? 0); ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errores['nombres']) ? 'is-invalid' : ''; ?>"
                           id="nombres" name="nombres" maxlength="65" required value="<?php echo e($valor('nombres')); ?>">
                    <?php if (isset($errores['nombres'])): ?><div class="invalid-feedback"><?php echo e($errores['nombres']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="apellidoPaterno" class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errores['apellidoPaterno']) ? 'is-invalid' : ''; ?>"
                           id="apellidoPaterno" name="apellidoPaterno" maxlength="30" required value="<?php echo e($valor('apellidoPaterno')); ?>">
                    <?php if (isset($errores['apellidoPaterno'])): ?><div class="invalid-feedback"><?php echo e($errores['apellidoPaterno']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="apellidoMaterno" class="form-label">Apellido materno</label>
                    <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno"
                           maxlength="30" value="<?php echo e($valor('apellidoMaterno')); ?>">
                </div>

                <div class="col-12 col-md-6">
                    <label for="rut" class="form-label">RUT <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errores['rut']) ? 'is-invalid' : ''; ?>"
                           id="rut" name="rut" placeholder="12.345.678-9" maxlength="13" required value="<?php echo e($valor('rut')); ?>">
                    <?php if (isset($errores['rut'])): ?><div class="invalid-feedback"><?php echo e($errores['rut']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono"
                           placeholder="+56 9 1234 5678" maxlength="13" value="<?php echo e($valor('telefono')); ?>">
                </div>

                <div class="col-12 col-md-6">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control <?php echo isset($errores['correo']) ? 'is-invalid' : ''; ?>"
                           id="correo" name="correo" maxlength="65" value="<?php echo e($valor('correo')); ?>">
                    <?php if (isset($errores['correo'])): ?><div class="invalid-feedback"><?php echo e($errores['correo']); ?></div><?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?php echo $esEdicion ? 'Guardar cambios' : 'Registrar cliente'; ?>
                </button>
                <a href="<?php echo e(url('clientes')); ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
