<?php
/**
 * ==========================================================================
 * ParkControl · app/views/reportes/index.php
 * ==========================================================================
 * Reporte de recaudación por período.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Reportes';
$seccion = $seccion ?? 'reportes';
require PC_APP . '/views/layout/header.php';

$maxDia = 0.0;
foreach ($porDia as $dia) {
    $maxDia = max($maxDia, (float) $dia['total']);
}
$etiquetasMetodo = [
    'efectivo'      => 'Efectivo',
    'debito'        => 'Débito',
    'credito'       => 'Crédito',
    'transferencia' => 'Transferencia',
];
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Reportes</h2>
        <p class="text-muted mb-0 small">Recaudación del <?php echo e(fechaCorta($desde)); ?> al <?php echo e(fechaCorta($hasta)); ?>.</p>
    </div>
    <a href="<?php echo e(url('reportes/exportar', ['desde' => $desde, 'hasta' => $hasta])); ?>" class="btn btn-outline-primary">
        <i class="bi bi-filetype-csv me-1"></i> Exportar CSV
    </a>
</div>

<div class="card pc-card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(BASE_URL . '/index.php'); ?>" class="row g-2 align-items-end">
            <input type="hidden" name="ruta" value="reportes">
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="<?php echo e($desde); ?>">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="<?php echo e($hasta); ?>">
            </div>
            <div class="col-12 col-md-3">
                <button class="btn btn-sm btn-primary" type="submit">Actualizar</button>
                <a href="<?php echo e(url('reportes')); ?>" class="btn btn-sm btn-outline-secondary">Mes actual</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php
    $tarjetas = [
        ['Ingresos cobrados', (string) $resumen['ingresos'], 'receipt', 'azul'],
        ['Recaudación', dinero($resumen['recaudado']), 'cash-coin', 'verde'],
        ['Ticket promedio', dinero($resumen['promedio']), 'graph-up', 'ambar'],
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

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-5">
        <div class="card pc-card h-100">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3">Por medio de pago</h3>
                <?php if ($porMetodo === []): ?>
                    <p class="text-muted small mb-0">Sin pagos en el período.</p>
                <?php else: ?>
                    <table class="table pc-tabla align-middle mb-0">
                        <tbody>
                            <?php foreach ($porMetodo as $fila): ?>
                                <tr>
                                    <td><?php echo e($etiquetasMetodo[$fila['metodo']] ?? $fila['metodo']); ?></td>
                                    <td class="text-muted small"><?php echo (int) $fila['pagos']; ?> pagos</td>
                                    <td class="text-end pc-monto"><?php echo e(dinero($fila['total'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card pc-card h-100">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3">Recaudación por día</h3>
                <?php if ($porDia === []): ?>
                    <p class="text-muted small mb-0">Sin pagos en el período.</p>
                <?php else: ?>
                    <?php foreach ($porDia as $dia): ?>
                        <?php $ancho = $maxDia > 0 ? max(2, (int) round((float) $dia['total'] * 100 / $maxDia)) : 0; ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small">
                                <span><?php echo e(fechaCorta($dia['dia'])); ?></span>
                                <strong><?php echo e(dinero($dia['total'])); ?></strong>
                            </div>
                            <div class="progress pc-progreso">
                                <div class="progress-bar bg-primary" style="width: <?php echo $ancho; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card pc-card">
    <div class="card-body">
        <h3 class="h6 fw-bold mb-3">Detalle de pagos</h3>
        <div class="table-responsive">
            <table class="table pc-tabla align-middle mb-0">
                <thead>
                    <tr>
                        <th>Pago</th>
                        <th>Fecha</th>
                        <th>Patente</th>
                        <th>Cliente</th>
                        <th>Método</th>
                        <th class="text-end">Monto</th>
                        <th class="text-end">Vuelto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pagos === []): ?>
                        <tr>
                            <td colspan="7">
                                <div class="pc-tabla-vacia">
                                    <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                                    No hay pagos registrados en este período.
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td class="small text-muted">#<?php echo (int) $p['idPago']; ?></td>
                            <td class="small"><?php echo e(fechaCorta($p['fechaPago']) . ' ' . horaCorta($p['fechaPago'])); ?></td>
                            <td class="fw-semibold"><?php echo e($p['patente']); ?></td>
                            <td class="small"><?php echo e(nombreCompleto($p)); ?></td>
                            <td class="small"><?php echo e($etiquetasMetodo[$p['metodo']] ?? $p['metodo']); ?></td>
                            <td class="text-end pc-monto"><?php echo e(dinero($p['monto'])); ?></td>
                            <td class="text-end small"><?php echo $p['vuelto'] !== null ? e(dinero($p['vuelto'])) : '—'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
