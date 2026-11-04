<?php
/**
 * ==========================================================================
 * ParkControl · app/views/plazas/index.php
 * ==========================================================================
 * Ocupación por fila. Cada fila tiene 16 espacios sin numeración.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Plazas';
$seccion = $seccion ?? 'plazas';
require PC_APP . '/views/layout/header.php';
?>

<div class="mb-4">
    <h2 class="h4 fw-bold mb-0">Plazas</h2>
    <p class="text-muted mb-0 small">
        Disponibilidad por fila. No hay numeración: al registrar un ingreso solo se indica la fila.
    </p>
</div>

<div class="row g-3 mb-4">
    <?php
    $tarjetas = [
        ['Espacios totales', $resumen['total'], 'grid-3x3-gap', 'azul'],
        ['Ocupados', $resumen['ocupadas'], 'car-front', 'rojo'],
        ['Disponibles', $resumen['libres'], 'check-circle', 'verde'],
        ['Filas llenas', $resumen['llenas'], 'cone-striped', 'ambar'],
    ];
    foreach ($tarjetas as [$etiqueta, $valor, $icono, $color]): ?>
        <div class="col-6 col-lg-3">
            <div class="card pc-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="pc-card-icono <?php echo e($color); ?>"><i class="bi bi-<?php echo e($icono); ?>"></i></div>
                    <div>
                        <div class="pc-metrica-etiqueta"><?php echo e($etiqueta); ?></div>
                        <div class="pc-metrica-valor"><?php echo (int) $valor; ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <?php foreach ($filas as $fila): ?>
        <?php
        $capacidad = max(1, (int) $fila['capacidadTotal']);
        $ocupadas = (int) $fila['ocupadas'];
        $libres = max(0, $capacidad - $ocupadas);
        $porcentaje = (int) round($ocupadas * 100 / $capacidad);
        $llena = $ocupadas >= $capacidad;
        ?>
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card pc-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="pc-metrica-etiqueta">Fila</div>
                            <div class="h4 fw-bold mb-0"><?php echo e($fila['fila']); ?></div>
                        </div>
                        <span class="pc-badge <?php echo $llena ? 'text-bg-danger' : 'text-bg-success'; ?>">
                            <?php echo $llena ? 'Llena' : 'Disponible'; ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span><?php echo $ocupadas; ?> ocupados</span>
                        <span><?php echo $libres; ?> libres de <?php echo $capacidad; ?></span>
                    </div>
                    <div class="progress pc-progreso mb-3" role="progressbar"
                         aria-valuenow="<?php echo $porcentaje; ?>" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar <?php echo $llena ? 'bg-danger' : 'bg-success'; ?>"
                             style="width: <?php echo $porcentaje; ?>%"></div>
                    </div>

                    <a href="<?php echo e(url('ingresos', ['estado' => 'abierto', 'q' => $fila['fila']])); ?>"
                       class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-car-front me-1"></i> Ver ingresos de la fila
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<p class="small text-muted mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i>
    Una fila se marca como llena cuando sus ingresos activos alcanzan la capacidad configurada.
</p>

<?php require PC_APP . '/views/layout/footer.php'; ?>
