<?php
/**
 * ==========================================================================
 * ParkControl · app/views/vehiculos/index.php
 * ==========================================================================
 * Listado de vehículos con búsqueda y acciones.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Vehículos';
$seccion = $seccion ?? 'vehiculos';
require PC_APP . '/views/layout/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Vehículos</h2>
        <p class="text-muted mb-0 small">Patentes registradas y su propietario.</p>
    </div>
    <a href="<?php echo e(url('vehiculos/crear')); ?>" class="btn btn-primary">
        <i class="bi bi-car-front me-1"></i> Nuevo vehículo
    </a>
</div>

<div class="card pc-card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo e(BASE_URL . '/index.php'); ?>" class="row g-2 align-items-center">
            <input type="hidden" name="ruta" value="vehiculos">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" class="form-control"
                           value="<?php echo e($busqueda); ?>"
                           placeholder="Buscar por patente, marca o dueño">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary" type="submit">Buscar</button>
                <?php if ($busqueda !== ''): ?>
                    <a href="<?php echo e(url('vehiculos')); ?>" class="btn btn-link">Limpiar</a>
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
                    <th>Patente</th>
                    <th>Vehículo</th>
                    <th>Año / Color</th>
                    <th>Propietario</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($vehiculos === []): ?>
                    <tr>
                        <td colspan="5">
                            <div class="pc-tabla-vacia">
                                <i class="bi bi-car-front fs-3 d-block mb-2"></i>
                                <?php echo $busqueda !== '' ? 'No se encontraron vehículos con ese criterio.' : 'Todavía no hay vehículos registrados.'; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($vehiculos as $v): ?>
                    <tr>
                        <td><span class="badge text-bg-dark fs-6"><?php echo e($v['patente']); ?></span></td>
                        <td>
                            <div class="fw-semibold"><?php echo e($v['marca'] . ' ' . $v['modelo']); ?></div>
                        </td>
                        <td class="small text-muted">
                            <?php echo e($v['anio'] ?? '—'); ?> · <?php echo e($v['color'] ?? '—'); ?>
                        </td>
                        <td>
                            <?php if (!empty($v['nombres'])): ?>
                                <?php echo e(nombreCompleto($v)); ?>
                            <?php else: ?>
                                <span class="text-muted small">Sin asignar</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="<?php echo e(url('vehiculos/editar', ['id' => $v['idVehiculo']])); ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?php echo e(url('vehiculos/eliminar')); ?>" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar el vehículo <?php echo e($v['patente']); ?>?');">
                                    <?php echo Csrf::campo(); ?>
                                    <input type="hidden" name="idVehiculo" value="<?php echo (int) $v['idVehiculo']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
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
