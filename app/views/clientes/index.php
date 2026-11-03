<?php
/**
 * ==========================================================================
 * ParkControl · app/views/clientes/index.php
 * ==========================================================================
 * Listado de clientes con búsqueda y acciones.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Clientes';
$seccion = $seccion ?? 'clientes';
require PC_APP . '/views/layout/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Clientes</h2>
        <p class="text-muted mb-0 small">Personas naturales o empresas dueñas de los vehículos.</p>
    </div>
    <a href="<?php echo e(url('clientes/crear')); ?>" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo cliente
    </a>
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
                                <a href="<?php echo e(url('clientes/editar', ['id' => $c['idCliente']])); ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?php echo e(url('clientes/estado')); ?>" class="d-inline"
                                      onsubmit="return confirm('¿<?php echo (int) $c['estado'] === 1 ? 'Desactivar' : 'Activar'; ?> a <?php echo e(nombreCompleto($c)); ?>?');">
                                    <?php echo Csrf::campo(); ?>
                                    <input type="hidden" name="idCliente" value="<?php echo (int) $c['idCliente']; ?>">
                                    <button type="submit" class="btn btn-sm <?php echo (int) $c['estado'] === 1 ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                            title="<?php echo (int) $c['estado'] === 1 ? 'Desactivar' : 'Activar'; ?>">
                                        <i class="bi bi-<?php echo (int) $c['estado'] === 1 ? 'slash-circle' : 'check-circle'; ?>"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
