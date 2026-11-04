<?php
/**
 * ==========================================================================
 * ParkControl · app/views/clientes/index.php
 * ==========================================================================
 * Listado de clientes con alta, edición y estado en modales Bootstrap 5.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Clientes';
$seccion = $seccion ?? 'clientes';
require PC_APP . '/views/layout/header.php';

$valor = static fn (string $campo, mixed $defecto = '') => $old[$campo] ?? $defecto;
$csrf = Csrf::campo();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Clientes</h2>
        <p class="text-muted mb-0 small">Personas naturales o empresas dueñas de los vehículos.</p>
    </div>
    <button type="button" class="btn btn-primary"
            data-form-modal="#modalCliente"
            data-modo="crear"
            data-form-action="<?php echo e(url('clientes/crear')); ?>"
            data-form-titulo="Nuevo cliente">
        <i class="bi bi-person-plus me-1"></i> Nuevo cliente
    </button>
</div>

<div class="card pc-card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo e(BASE_URL . '/index.php'); ?>" class="row g-2 align-items-center">
            <input type="hidden" name="ruta" value="clientes">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" class="form-control"
                           value="<?php echo e($busqueda); ?>"
                           placeholder="Buscar por nombre, RUT o correo">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
                <?php if ($busqueda !== ''): ?>
                    <a href="<?php echo e(url('clientes')); ?>" class="btn btn-link">Limpiar</a>
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
                    <th>Cliente</th>
                    <th>RUT</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($clientes === []): ?>
                    <tr>
                        <td colspan="5">
                            <div class="pc-tabla-vacia">
                                <i class="bi bi-people fs-3 d-block mb-2"></i>
                                <?php echo $busqueda !== '' ? 'No se encontraron clientes con ese criterio.' : 'Todavía no hay clientes registrados.'; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($clientes as $c): ?>
                    <?php
                    $campos = [
                        'idCliente'       => (string) $c['idCliente'],
                        'rut'             => $c['rut'],
                        'nombres'         => $c['nombres'],
                        'apellidoPaterno' => $c['apellidoPaterno'],
                        'apellidoMaterno' => (string) ($c['apellidoMaterno'] ?? ''),
                        'telefono'        => (string) ($c['telefono'] ?? ''),
                        'correo'          => (string) ($c['correo'] ?? ''),
                    ];
                    ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?php echo e(nombreCompleto($c)); ?></div>
                            <div class="small text-muted">Registrado el <?php echo e(fechaCorta($c['creadoEn'])); ?></div>
                        </td>
                        <td><?php echo e($c['rut']); ?></td>
                        <td>
                            <div class="small"><?php echo e($c['telefono'] ?? '—'); ?></div>
                            <div class="small text-muted"><?php echo e($c['correo'] ?? '—'); ?></div>
                        </td>
                        <td>
                            <?php if ((int) $c['estado'] === 1): ?>
                                <span class="pc-badge text-bg-success">Activo</span>
                            <?php else: ?>
                                <span class="pc-badge text-bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Editar"
                                        data-form-modal="#modalCliente"
                                        data-modo="editar"
                                        data-form-action="<?php echo e(url('clientes/actualizar')); ?>"
                                        data-form-titulo="Editar cliente"
                                        data-campos="<?php echo e(json_encode($campos)); ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <button type="button" class="btn btn-sm <?php echo (int) $c['estado'] === 1 ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                        title="<?php echo (int) $c['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>"
                                        data-confirmar
                                        data-confirm-action="<?php echo e(url('clientes/estado')); ?>"
                                        data-confirm-campo="idCliente"
                                        data-confirm-valor="<?php echo (int) $c['idCliente']; ?>"
                                        data-confirm-titulo="<?php echo (int) $c['estado'] === 1 ? 'Desactivar cliente' : 'Activar cliente'; ?>"
                                        data-confirm-mensaje="<?php echo (int) $c['estado'] === 1 ? 'El cliente quedará inactivo, pero su historial se conserva.' : 'El cliente volverá a estar disponible para nuevos ingresos.'; ?>"
                                        data-confirm-aceptar="<?php echo (int) $c['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>"
                                        data-confirm-clase="<?php echo (int) $c['estado'] === 1 ? 'btn-danger' : 'btn-success'; ?>">
                                    <i class="bi bi-<?php echo (int) $c['estado'] === 1 ? 'slash-circle' : 'check-circle'; ?>"></i>
                                </button>
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
    Las altas y ediciones se hacen en ventanas modales; la baja es lógica (estado inactivo).
</p>

<!-- Modal: crear / editar cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(url('clientes/crear')); ?>"
                  data-action-crear="<?php echo e(url('clientes/crear')); ?>" novalidate>
                <?php echo $csrf; ?>
                <input type="hidden" name="idCliente" value="<?php echo e($valor('idCliente', '')); ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteTitulo"
                        data-modal-titulo data-titulo-crear="Nuevo cliente">Nuevo cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <?php if ($errores !== []): ?>
                        <div class="alert alert-danger small"><?php echo e(reset($errores)); ?></div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="mcNombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mcNombres" name="nombres"
                                   maxlength="65" required value="<?php echo e($valor('nombres')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="mcApellidoPaterno" class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mcApellidoPaterno" name="apellidoPaterno"
                                   maxlength="30" required value="<?php echo e($valor('apellidoPaterno')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="mcApellidoMaterno" class="form-label">Apellido materno</label>
                            <input type="text" class="form-control" id="mcApellidoMaterno" name="apellidoMaterno"
                                   maxlength="30" value="<?php echo e($valor('apellidoMaterno')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="mcRut" class="form-label">RUT <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="mcRut" name="rut"
                                   placeholder="12.345.678-9" maxlength="13" required value="<?php echo e($valor('rut')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="mcTelefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="mcTelefono" name="telefono"
                                   maxlength="13" placeholder="+56912345678" value="<?php echo e($valor('telefono')); ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="mcCorreo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="mcCorreo" name="correo"
                                   maxlength="65" value="<?php echo e($valor('correo')); ?>">
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
