<?php
/**
 * ==========================================================================
 * ParkControl · app/views/ingresos/index.php
 * ==========================================================================
 * Listado de ingresos con filtros y acciones de salida/anulación.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Ingresos';
$seccion = $seccion ?? 'ingresos';
require PC_APP . '/views/layout/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Ingresos</h2>
        <p class="text-muted mb-0 small">Entradas, salidas y cobros del estacionamiento.</p>
    </div>
    <a href="<?php echo e(url('ingresos/crear')); ?>" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right me-1"></i> Registrar ingreso
    </a>
</div>

<div class="row g-3 mb-4">
    <?php
    $tarjetas = [
        ['Vehículos dentro', $resumen['abiertos'], 'car-front', 'azul'],
        ['Ingresos de hoy', $resumen['hoy'], 'box-arrow-in-right', 'verde'],
        ['Cobrado hoy', dinero($resumen['cobrado']), 'cash-coin', 'ambar'],
    ];
    foreach ($tarjetas as [$etiqueta, $valor, $icono, $color]): ?>
        <div class="col-12 col-md-4">
            <div class="card pc-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="pc-card-icono <?php echo e($color); ?>"><i class="bi bi-<?php echo e($icono); ?>"></i></div>
                    <div>
                        <div class="pc-metrica-etiqueta"><?php echo e($etiqueta); ?></div>
                        <div class="pc-metrica-valor"><?php echo e($valor); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card pc-card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo e(BASE_URL . '/index.php'); ?>" class="row g-2 align-items-end">
            <input type="hidden" name="ruta" value="ingresos">
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Estado</label>
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <?php foreach (['abierto' => 'Abiertos', 'cerrado' => 'Cerrados', 'anulado' => 'Anulados'] as $valor => $texto): ?>
                        <option value="<?php echo $valor; ?>" <?php echo $estado === $valor ? 'selected' : ''; ?>>
                            <?php echo $texto; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="<?php echo e($desde); ?>">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?php echo e($hasta); ?>">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="search" name="q" class="form-control form-control-sm"
                       value="<?php echo e($busqueda); ?>" placeholder="Patente, cliente o plaza">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary flex-grow-1" type="submit">Filtrar</button>
                <a href="<?php echo e(url('ingresos')); ?>" class="btn btn-sm btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card pc-card">
    <div class="table-responsive">
        <table class="table pc-tabla align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Vehículo</th>
                    <th>Cliente</th>
                    <th>Plaza</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Estado</th>
                    <th class="text-end">Monto</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ingresos === []): ?>
                    <tr>
                        <td colspan="9">
                            <div class="pc-tabla-vacia">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No hay ingresos que coincidan con el filtro.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($ingresos as $i): ?>
                    <tr>
                        <td class="text-muted small"><?php echo (int) $i['idIngreso']; ?></td>
                        <td class="fw-semibold"><?php echo e($i['patente']); ?></td>
                        <td class="small"><?php echo e(nombreCompleto($i)); ?></td>
                        <td><?php echo e($i['plaza'] ?? '—'); ?></td>
                        <td class="small">
                            <?php echo e(fechaCorta($i['fechaInicio'])); ?>
                            <span class="text-muted"><?php echo e(horaCorta($i['horaInicio'])); ?></span>
                        </td>
                        <td class="small">
                            <?php echo $i['fechaTermino'] ? e(fechaCorta($i['fechaTermino']) . ' ' . horaCorta($i['horaTermino'])) : '—'; ?>
                        </td>
                        <td>
                            <?php if ($i['estado'] === 'abierto'): ?>
                                <span class="pc-badge text-bg-success">Dentro</span>
                            <?php elseif ($i['estado'] === 'cerrado'): ?>
                                <span class="pc-badge text-bg-secondary">Cerrado</span>
                            <?php else: ?>
                                <span class="pc-badge text-bg-danger">Anulado</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pc-monto"><?php echo $i['montoTotal'] !== null ? e(dinero($i['montoTotal'])) : '—'; ?></td>
                        <td class="text-end">
                            <?php if ($i['estado'] === 'abierto'): ?>
                                <div class="d-inline-flex gap-1">
                                    <a href="<?php echo e(url('ingresos/salida', ['id' => $i['idIngreso']])); ?>"
                                       class="btn btn-sm btn-success" title="Registrar salida">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(url('ingresos/anular')); ?>" class="d-inline"
                                          onsubmit="return confirm('¿Anular el ingreso de <?php echo e($i['patente']); ?>?');">
                                        <?php echo Csrf::campo(); ?>
                                        <input type="hidden" name="idIngreso" value="<?php echo (int) $i['idIngreso']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Anular">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo e(url('ingresos/salida', ['id' => $i['idIngreso']])); ?>"
                                   class="btn btn-sm btn-outline-secondary" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
