<?php
/**
 * ==========================================================================
 * ParkControl · app/views/usuarios/form.php
 * ==========================================================================
 * Formulario de alta y edición de usuarios.
 * Variables: $modo, $usuario, $roles, $errores, $old.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Usuario';
$seccion = $seccion ?? 'usuarios';
require PC_APP . '/views/layout/header.php';

$esEdicion = $modo === 'editar';
$valor = static function (string $campo, mixed $defecto = '') use ($old, $usuario): mixed {
    return $old[$campo] ?? $usuario[$campo] ?? $defecto;
};
$accion = $esEdicion ? url('usuarios/actualizar') : url('usuarios/crear');
?>

<div class="mb-4">
    <a href="<?php echo e(url('usuarios')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0">
        <?php echo $esEdicion ? 'Editar usuario' : 'Nuevo usuario'; ?>
    </h2>
    <p class="text-muted small mb-0">
        <?php echo $esEdicion ? 'Actualiza los datos y el rol de la cuenta.' : 'Crea una cuenta y entrega la contraseña inicial al usuario.'; ?>
    </p>
</div>

<div class="card pc-card pc-form-card">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo e($accion); ?>" novalidate>
            <?php echo Csrf::campo(); ?>
            <?php if ($esEdicion): ?>
                <input type="hidden" name="idUsuario" value="<?php echo (int) ($usuario['idUsuario'] ?? 0); ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errores['nombres']) ? 'is-invalid' : ''; ?>"
                           id="nombres" name="nombres" maxlength="65" required
                           value="<?php echo e($valor('nombres')); ?>">
                    <?php if (isset($errores['nombres'])): ?>
                        <div class="invalid-feedback"><?php echo e($errores['nombres']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="apellidoPaterno" class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errores['apellidoPaterno']) ? 'is-invalid' : ''; ?>"
                           id="apellidoPaterno" name="apellidoPaterno" maxlength="30" required
                           value="<?php echo e($valor('apellidoPaterno')); ?>">
                    <?php if (isset($errores['apellidoPaterno'])): ?>
                        <div class="invalid-feedback"><?php echo e($errores['apellidoPaterno']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="apellidoMaterno" class="form-label">Apellido materno</label>
                    <input type="text" class="form-control <?php echo isset($errores['apellidoMaterno']) ? 'is-invalid' : ''; ?>"
                           id="apellidoMaterno" name="apellidoMaterno" maxlength="30"
                           value="<?php echo e($valor('apellidoMaterno')); ?>">
                    <?php if (isset($errores['apellidoMaterno'])): ?>
                        <div class="invalid-feedback"><?php echo e($errores['apellidoMaterno']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="rut" class="form-label">RUT <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php echo isset($errores['rut']) ? 'is-invalid' : ''; ?>"
                           id="rut" name="rut" placeholder="12.345.678-9" maxlength="13" required
                           value="<?php echo e($valor('rut')); ?>">
                    <?php if (isset($errores['rut'])): ?>
                        <div class="invalid-feedback"><?php echo e($errores['rut']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="correo" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" class="form-control <?php echo isset($errores['correo']) ? 'is-invalid' : ''; ?>"
                           id="correo" name="correo" maxlength="65" required
                           value="<?php echo e($valor('correo')); ?>">
                    <?php if (isset($errores['correo'])): ?>
                        <div class="invalid-feedback"><?php echo e($errores['correo']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="idRol" class="form-label">Rol <span class="text-danger">*</span></label>
                    <select class="form-select <?php echo isset($errores['idRol']) ? 'is-invalid' : ''; ?>"
                            id="idRol" name="idRol" required>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo (int) $rol['idRol']; ?>"
                                <?php echo (int) $valor('idRol', 2) === (int) $rol['idRol'] ? 'selected' : ''; ?>>
                                <?php echo e($rol['rol']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['idRol'])): ?>
                        <div class="invalid-feedback"><?php echo e($errores['idRol']); ?></div>
                    <?php endif; ?>
                </div>

                <?php if (!$esEdicion): ?>
                    <div class="col-12 col-md-6">
                        <label for="contrasena" class="form-label">Contraseña inicial <span class="text-danger">*</span></label>
                        <input type="password" class="form-control <?php echo isset($errores['contrasena']) ? 'is-invalid' : ''; ?>"
                               id="contrasena" name="contrasena" minlength="8" required autocomplete="new-password">
                        <div class="form-text">Mínimo 8 caracteres.</div>
                        <?php if (isset($errores['contrasena'])): ?>
                            <div class="invalid-feedback"><?php echo e($errores['contrasena']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Contraseña</label>
                        <div class="form-control-plaintext small text-muted">
                            Por seguridad no se edita aquí. Usa “Restablecer” en el listado.
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    <?php echo $esEdicion ? 'Guardar cambios' : 'Crear usuario'; ?>
                </button>
                <a href="<?php echo e(url('usuarios')); ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
