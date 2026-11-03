<?php
/**
 * ==========================================================================
 * ParkControl · app/views/usuarios/index.php
 * ==========================================================================
 * Listado y acciones de usuarios del sistema.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Usuarios';
$seccion = $seccion ?? 'usuarios';
require PC_APP . '/views/layout/header.php';

$yo = Auth::usuario();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Gestión de usuarios</h2>
        <p class="text-muted mb-0 small">Crea cuentas, asigna roles y controla el acceso al sistema.</p>
    </div>
    <a href="<?php echo e(url('usuarios/crear')); ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo usuario
    </a>
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
                    <?php $esYo = (int) $u['idUsuario'] === (int) $yo['id']; ?>
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
                                <a href="<?php echo e(url('usuarios/editar', ['id' => $u['idUsuario']])); ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form method="POST" action="<?php echo e(url('usuarios/restablecer')); ?>" class="d-inline"
                                      onsubmit="return confirm('¿Restablecer la contraseña de <?php echo e(nombreCompleto($u)); ?>?');">
                                    <?php echo Csrf::campo(); ?>
                                    <input type="hidden" name="idUsuario" value="<?php echo (int) $u['idUsuario']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Restablecer contraseña">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </form>

                                <?php if (!$esYo): ?>
                                    <form method="POST" action="<?php echo e(url('usuarios/estado')); ?>" class="d-inline"
                                          onsubmit="return confirm('¿<?php echo (int) $u['estado'] === 1 ? 'Desactivar' : 'Activar'; ?> a <?php echo e(nombreCompleto($u)); ?>?');">
                                        <?php echo Csrf::campo(); ?>
                                        <input type="hidden" name="idUsuario" value="<?php echo (int) $u['idUsuario']; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo (int) $u['estado'] === 1 ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                                title="<?php echo (int) $u['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>">
                                            <i class="bi bi-<?php echo (int) $u['estado'] === 1 ? 'slash-circle' : 'check-circle'; ?>"></i>
                                        </button>
                                    </form>
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
    Restablecer una contraseña genera una clave temporal que se muestra una sola vez.
</p>

<?php require PC_APP . '/views/layout/footer.php'; ?>
