<?php
/**
 * ==========================================================================
 * ParkControl · app/views/plazas/index.php
 * ==========================================================================
 * Mapa de ocupación por fila y administración de filas/plazas.
 * Variables: $filas, $plazas, $resumen.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Plazas';
$seccion = $seccion ?? 'plazas';
require PC_APP . '/views/layout/header.php';

$colores = [
    Plaza::LIBRE => ['clase' => 'libre', 'texto' => 'Libre'],
    Plaza::OCUPADA => ['clase' => 'ocupada', 'texto' => 'Ocupada'],
    Plaza::FUERA_DE_SERVICIO => ['clase' => 'fuera', 'texto' => 'Fuera'],
];
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Plazas</h2>
        <p class="text-muted mb-0 small">Estado del estacionamiento por fila o sector.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(url('plazas/filas/crear')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-plus-square me-1"></i> Nueva fila
        </a>
        <a href="<?php echo e(url('plazas/crear')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva plaza
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php
    $resumenTarjetas = [
        ['Plazas totales', $resumen['total'], 'grid-3x3-gap', 'azul'],
        ['Libres', $resumen['libres'], 'check-circle', 'verde'],
        ['Ocupadas', $resumen['ocupadas'], 'car-front', 'rojo'],
        ['Fuera de servicio', $resumen['fuera'], 'cone-striped', 'ambar'],
    ];
    foreach ($resumenTarjetas as [$etiqueta, $valor, $icono, $color]): ?>
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

<?php if ($filas === []): ?>
    <div class="card pc-card">
        <div class="pc-tabla-vacia">
            <i class="bi bi-grid-3x3-gap fs-3 d-block mb-2"></i>
            Aún no hay filas ni plazas configuradas.
        </div>
    </div>
<?php endif; ?>

<?php foreach ($plazas as $grupo): ?>
    <div class="card pc-card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 fw-bold mb-0">
                    <i class="bi bi-signpost-split me-1 text-primary"></i>
                    Fila <?php echo e($grupo['fila']['fila']); ?>
                </h3>
                <span class="small text-muted"><?php echo count($grupo['plazas']); ?> plazas</span>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($grupo['plazas'] as $plaza): ?>
                    <?php
                    $estado = (int) $plaza['estado'];
                    $info = $colores[$estado] ?? $colores[Plaza::LIBRE];
                    $bloqueada = $estado === Plaza::OCUPADA;
                    ?>
                    <form method="POST" action="<?php echo e(url('plazas/estado')); ?>" class="d-inline">
                        <?php echo Csrf::campo(); ?>
                        <input type="hidden" name="idPlaza" value="<?php echo (int) $plaza['idPlaza']; ?>">
                        <button type="submit"
                                class="pc-plaza pc-plaza-<?php echo e($info['clase']); ?>"
                                <?php echo $bloqueada ? 'disabled' : ''; ?>
                                title="<?php echo e($plaza['numero'] . ' · ' . $info['texto']); ?>">
                            <?php echo e($plaza['numero']); ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<div class="card pc-card mt-3">
    <div class="card-body">
        <h3 class="h6 fw-bold mb-2">Filas registradas</h3>
        <div class="table-responsive">
            <table class="table pc-tabla align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fila</th>
                        <th>Capacidad</th>
                        <th>Plazas creadas</th>
                        <th>Libres</th>
                        <th>Ocupadas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $f): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($f['fila']); ?></td>
                            <td><?php echo (int) $f['capacidadTotal']; ?></td>
                            <td><?php echo (int) $f['plazas']; ?></td>
                            <td><?php echo (int) $f['libres']; ?></td>
                            <td><?php echo (int) $f['ocupadas']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="small text-muted mt-3 mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Haz clic en una plaza libre para ponerla fuera de servicio (o al revés). Las plazas ocupadas
            se liberan al registrar la salida del vehículo.
        </p>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
