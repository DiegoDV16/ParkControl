<?php
/**
 * ==========================================================================
 * ParkControl · app/views/tarifas/index.php
 * ==========================================================================
 * Tabla de precios vigente: estadía por día y servicios por tipo de vehículo.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Tarifas';
$seccion = $seccion ?? 'tarifas';
require PC_APP . '/views/layout/header.php';
?>

<div class="mb-4">
    <h2 class="h4 fw-bold mb-0">Tarifas</h2>
    <p class="text-muted mb-0 small">
        Precios vigentes de estadía y servicios. El cobro se calcula al registrar la salida.
    </p>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card pc-card h-100">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3">Estadía por día</h3>
                <table class="table pc-tabla align-middle mb-3">
                    <thead>
                        <tr>
                            <th>Días</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1 a 2 días</td>
                            <td class="text-end pc-monto"><?php echo e(dinero(Tarifa::ESTADIA['dia_1_2'])); ?> / día</td>
                        </tr>
                        <tr>
                            <td>3 a 5 días</td>
                            <td class="text-end pc-monto"><?php echo e(dinero(Tarifa::ESTADIA['dia_3_5'])); ?> / día</td>
                        </tr>
                        <tr>
                            <td>6 a 7 días</td>
                            <td class="text-end pc-monto"><?php echo e(dinero(Tarifa::ESTADIA['fijo_6_7'])); ?> fijo</td>
                        </tr>
                        <tr>
                            <td>8 días o más</td>
                            <td class="text-end pc-monto"><?php echo e(dinero(Tarifa::ESTADIA['fijo_8_mas'])); ?> fijo</td>
                        </tr>
                    </tbody>
                </table>

                <div class="alert alert-light border small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Una estadía de menos de un día se cobra como 1 día. Después de cada día completo
                    se consideran 5 horas de prórroga antes de cobrar un día adicional.
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card pc-card h-100">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3">Servicio extra según tipo de vehículo</h3>
                <div class="table-responsive">
                    <table class="table pc-tabla align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th class="text-end">Lavado</th>
                                <th class="text-end">Lavado y aspirado</th>
                                <th class="text-end">Aspirado</th>
                                <th class="text-end">Sin servicio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (Tarifa::SERVICIOS as $tipo => $precios): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($tipo); ?></td>
                                    <td class="text-end pc-monto"><?php echo e(dinero($precios['Lavado'])); ?></td>
                                    <td class="text-end pc-monto"><?php echo e(dinero($precios['Lavado y aspirado'])); ?></td>
                                    <td class="text-end pc-monto"><?php echo e(dinero($precios['Aspirado'])); ?></td>
                                    <td class="text-end pc-monto"><?php echo e(dinero($precios['Sin servicio'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <p class="small text-muted mt-3 mb-0">
                    El total de la salida es la suma de la estadía más el servicio del vehículo.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
