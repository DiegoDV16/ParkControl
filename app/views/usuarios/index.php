<?php
/**
 * ==========================================================================
 * ParkControl · app/views/usuarios/index.php
 * ==========================================================================
 * Listado de usuarios con alta, edición y acciones en modales Bootstrap 5.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Usuarios';
$seccion = $seccion ?? 'usuarios';
require PC_APP . '/views/layout/header.php';

$yo = Auth::usuario();
$valor = static fn (string $campo, mixed $defecto = '') => $old[$campo] ?? $defecto;
$csrf = Csrf::campo();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Gestión de usuarios</h2>
        <p class="text-muted mb-0 small">Crea cuentas, asigna roles y controla el acceso al sistema.</p>
    </div>
    <button type="button" class="btn btn-primary"
            data-form-modal="#modalUsuario"
            data-modo="crear"
            data-form-action="<?php echo e(url('usuarios/crear')); ?>"
            data-form-titulo="Nuevo usuario">
        <i class="bi bi-person-plus me-1"></i> Nuevo usuario
    </button>
</div>

<div class="card pc-card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo e(BASE_URL . '/index.php'); ?>" class="row g-2 align-items-center">
            <input type="hidden" name="ruta" value="usuarios">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" class="form-control"
                           value="<?php echo e($busqueda); ?>"
                           placeholder="Buscar por nombre, correo o RUT">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
                <?php if ($busqueda !== ''): ?>
                    <a href="<?php echo e(url('usuarios')); ?>" class="btn btn-link">Limpiar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card pc-card">
    <div class="table-responsive">
        <table class="table pc-tabla align-middle mb-0">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>RUT</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último acceso</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usuarios === []): ?>
                    <tr>
                        <td colspan="6">
                            <div class="pc-tabla-vacia">
                                <i class="bi bi-person-x fs-3 d-block mb-2"></i>
                                No se encontraron usuarios con ese criterio.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($usuarios as $u): ?>
                    <?php
                    $esYo = (int) $u['idUsuario'] === (int) $yo['id'];
                    $campos = [
                        'idUsuario'       => (string) $u['idUsuario'],
                        'idRol'           => (string) $u['idRol'],
                        'rut'             => $u['rut'],
                        'nombres'         => $u['nombres'],
                        'apellidoPaterno' => $u['apellidoPaterno'],
                        'apellidoMaterno' => (string) ($u['apellidoMaterno'] ?? ''),
                        'correo'          => $u['correo'],
                    ];
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="pc-avatar"><?php echo e(iniciales(nombreCompleto($u))); ?></span>
                                <div>
                                    <div class="fw-semibold"><?php echo e(nombreCompleto($u)); ?></div>
                                    <div class="small text-muted"><?php echo e($u['correo']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?php echo e($u['rut']); ?></td>
                        <td>
                            <span class="pc-badge <?php echo $u['rolNombre'] === 'Administrador' ? 'text-bg-primary' : 'text-bg-info'; ?>">
                                <?php echo e($u['rolNombre']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ((int) $u['estado'] === 1): ?>
                                <span class="pc-badge text-bg-success">Activo</span>
                            <?php else: ?>
                                <span class="pc-badge text-bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted">
                            <?php echo $u['ultimoAcceso'] ? e(fechaCorta($u['ultimoAcceso']) . ' ' . horaCorta($u['ultimoAcceso'])) : 'Nunca'; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Editar"
                                        data-form-modal="#modalUsuario"
                                        data-modo="editar"
                                        data-form-action="<?php echo e(url('usuarios/actualizar')); ?>"
                                        data-form-titulo="Editar usuario"
                                        data-campos="<?php echo e(json_encode($campos)); ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-warning" title="Restablecer contraseña"
                                        data-confirmar
                                        data-confirm-action="<?php echo e(url('usuarios/restablecer')); ?>"
                                        data-confirm-campo="idUsuario"
                                        data-confirm-valor="<?php echo (int) $u['idUsuario']; ?>"
                                        data-confirm-titulo="Restablecer contraseña"
                                        data-confirm-mensaje="Se generará una clave temporal para <?php echo e(nombreCompleto($u)); ?> y se mostrará una sola vez."
                                        data-confirm-aceptar="Restablecer"
                                        data-confirm-clase="btn-warning">
                                    <i class="bi bi-key"></i>
                                </button>

                                <?php if (!$esYo): ?>
                                    <button type="button" class="btn btn-sm <?php echo (int) $u['estado'] === 1 ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                            title="<?php echo (int) $u['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>"
                                            data-confirmar
                                            data-confirm-action="<?php echo e(url('usuarios/estado')); ?>"
                                            data-confirm-campo="idUsuario"
                                            data-confirm-valor="<?php echo (int) $u['idUsuario']; ?>"
                                            data-confirm-titulo="<?php echo (int) $u['estado'] === 1 ? 'Desactivar usuario' : 'Activar usuario'; ?>"
                                            data-confirm-mensaje="<?php echo (int) $u['estado'] === 1 ? 'La cuenta dejará de poder iniciar sesión.' : 'La cuenta podrá volver a iniciar sesión.'; ?>"
                                            data-confirm-aceptar="<?php echo (int) $u['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>"
                                            data-confirm-clase="<?php echo (int) $u['estado'] === 1 ? 'btn-danger' : 'btn-success'; ?>">
                                        <i class="bi bi-<?php echo (int) $u['estado'] === 1 ? 'slash-circle' : 'check-circle'; ?>"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<p class="small text-muted mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i>
    Las altas, ediciones y cambios de estado se confirman en ventanas modales.
</p>

<!-- Modal: crear / editar usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(url('usuarios/crear')); ?>"
                  data-action-crear="<?php echo e(url('usuarios/crear')); ?>" novalidate>
                <?php echo $csrf; ?>
                <input type="hidden" name="idUsuario" value="<?php echo e($valor('idUsuario', '')); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalUsuarioTitulo"
                        data-modal-titulo data-titulo-crear="Nuevo usuario">Nuevo usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <?php if ($errores !== []): ?>
                        <div class="alert alert-danger small"><?php echo e(reset($errores)); ?></div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="muNombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="muNombres" name="nombres"
                                   maxlength="65" required value="<?php echo e($valor('nombres')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="muApellidoPaterno" class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="muApellidoPaterno" name="apellidoPaterno"
                                   maxlength="30" required value="<?php echo e($valor('apellidoPaterno')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="muApellidoMaterno" class="form-label">Apellido materno</label>
                            <input type="text" class="form-control" id="muApellidoMaterno" name="apellidoMaterno"
                                   maxlength="30" value="<?php echo e($valor('apellidoMaterno')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="muRut" class="form-label">RUT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="muRut" name="rut"
                                   placeholder="12.345.678-9" maxlength="13" required value="<?php echo e($valor('rut')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="muCorreo" class="form-label">Correo <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="muCorreo" name="correo"
                                   maxlength="65" required value="<?php echo e($valor('correo')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="muRol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="muRol" name="idRol" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo (int) $rol['idRol']; ?>"
                                        <?php echo (int) $valor('idRol', 2) === (int) $rol['idRol'] ? 'selected' : ''; ?>>
                                        <?php echo e($rol['rol']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6" data-solo-crear>
                            <label for="muContrasena" class="form-label">Contraseña inicial <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="muContrasena" name="contrasena"
                                   minlength="8" autocomplete="new-password">
                            <div class="form-text">Mínimo 8 caracteres. Solo al crear la cuenta.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: confirmación de acciones -->
<div class="modal fade" id="modalConfirmar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST" action="">
                <?php echo $csrf; ?>
                <input type="hidden" name="id" value="" data-confirmar-campo>

                <div class="modal-header">
                    <h5 class="modal-title" data-confirmar-titulo>Confirmar acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body small" data-confirmar-mensaje>¿Confirmas esta acción?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" data-confirmar-aceptar>Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
