<?php
/**
 * ==========================================================================
 * ParkControl · app/views/dashboard/index.php
 * ==========================================================================
 * Panel principal con indicadores reales de la base de datos.
 * Variables: $plazas, $ingresosAbiertos, $ingresosHoy, $recaudacionHoy,
 *            $ultimosIngresos, $totalUsuarios, $ocupacion.
 * ==========================================================================
 */

$titulo  = 'Inicio';
$seccion = 'dashboard';
require PC_APP . '/views/layout/header.php';

$metricas = [
    [
        'etiqueta' => 'Vehículos dentro',
        'valor'    => (string) $ingresosAbiertos,
        'detalle'  => 'Ingresos sin salida registrada',
        'icono'    => 'car-front',
        'color'    => 'azul',
    ],
    [
        'etiqueta' => 'Ingresos de hoy',
        'valor'    => (string) $ingresosHoy,
        'detalle'  => 'Registrados el ' . date('d-m-Y'),
        'icono'    => 'box-arrow-in-right',
        'color'    => 'verde',
    ],
    [
        'etiqueta' => 'Recaudación de hoy',
        'valor'    => dinero($recaudacionHoy),
        'detalle'  => 'Pagos recibidos hoy',
        'icono'    => 'cash-coin',
        'color'    => 'ambar',
    ],
    [
        'etiqueta' => 'Plazas libres',
        'valor'    => $plazas['libres'] . ' / ' . $plazas['total'],
        'detalle'  => $plazas['total'] > 0
            ? $ocupacion . '% de ocupación'
            : 'Aún no hay plazas configuradas',
        'icono'    => 'grid-3x3-gap',
        'color'    => 'rojo',
    ],
];
?>

<div class="row g-3 mb-4">
    <?php foreach ($metricas as $metrica): ?>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card pc-card h-100">
                <div class="card-body d-flex align-items-start gap-3">
                    <div class="pc-card-icono <?php echo e($metrica['color']); ?>">
                        <i class="bi bi-<?php echo e($metrica['icono']); ?>"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="pc-metrica-etiqueta"><?php echo e($metrica['etiqueta']); ?></div>
                        <div class="pc-metrica-valor"><?php echo e($metrica['valor']); ?></div>
                        <div class="small text-muted text-truncate"><?php echo e($metrica['detalle']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="card pc-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h6 fw-bold mb-0">Últimos ingresos</h2>
                        <span class="small text-muted">Movimientos más recientes del estacionamiento</span>
                    </div>
                    <span class="pc-badge text-bg-light"><?php echo count($ultimosIngresos); ?> registros</span>
                </div>

                <div class="table-responsive">
                    <table class="table pc-tabla align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Vehículo</th>
                                <th>Cliente</th>
                                <th>Fila</th>
                                <th>Entrada</th>
                                <th>Estado</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($ultimosIngresos === []): ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="pc-tabla-vacia">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                            Todavía no hay ingresos registrados.
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimosIngresos as $ingreso): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo e($ingreso['patente']); ?></td>
                                        <td><?php echo e(nombreCompleto($ingreso)); ?></td>
                                        <td><?php echo e($ingreso['fila'] ?? '—'); ?></td>
                                        <td>
                                            <?php echo e(fechaCorta($ingreso['fechaInicio'])); ?>
                                            <span class="text-muted small"><?php echo e(horaCorta($ingreso['horaInicio'])); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($ingreso['estado'] === 'abierto'): ?>
                                                <span class="pc-badge text-bg-success">Dentro</span>
                                            <?php elseif ($ingreso['estado'] === 'cerrado'): ?>
                                                <span class="pc-badge text-bg-secondary">Cerrado</span>
                                            <?php else: ?>
                                                <span class="pc-badge text-bg-danger">Anulado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php echo $ingreso['montoTotal'] !== null ? e(dinero($ingreso['montoTotal'])) : '—'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card pc-card mb-3">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">Ocupación del estacionamiento</h2>

                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Plazas ocupadas</span>
                    <span><?php echo $plazas['ocupadas']; ?> de <?php echo $plazas['total']; ?></span>
                </div>
                <div class="progress pc-progreso mb-3" role="progressbar"
                     aria-valuenow="<?php echo $ocupacion; ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-success" style="width: <?php echo $ocupacion; ?>%"></div>
                </div>

                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-1">
                        <span><i class="bi bi-circle-fill text-success me-1" style="font-size:.5rem;"></i> Libres</span>
                        <strong><?php echo $plazas['libres']; ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span><i class="bi bi-circle-fill text-danger me-1" style="font-size:.5rem;"></i> Ocupadas</span>
                        <strong><?php echo $plazas['ocupadas']; ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span><i class="bi bi-circle-fill text-warning me-1" style="font-size:.5rem;"></i> Filas llenas</span>
                        <strong><?php echo $plazas['fuera']; ?></strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card pc-card">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">Resumen del sistema</h2>
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Usuarios registrados</span>
                        <strong><?php echo $totalUsuarios ?? '—'; ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Plazas configuradas</span>
                        <strong><?php echo $plazas['total']; ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Sesión iniciada como</span>
                        <strong><?php echo e(Auth::usuario()['rolNombre'] ?? ''); ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
