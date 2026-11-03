<?php
/**
 * ==========================================================================
 * ParkControl · app/views/tarifas/index.php
 * ==========================================================================
 * Listado de tarifas de cobro.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Tarifas';
$seccion = $seccion ?? 'tarifas';
require PC_APP . '/views/layout/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Tarifas</h2>
        <p class="text-muted mb-0 small">Valores de cobro por hora o fracción. La tarifa activa más antigua se usa por defecto.</p>
    </div>
    <a href="<?php echo e(url('tarifas/crear')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva tarifa
    </a>
</div>

<div class="card pc-card">
    <div class="table-responsive">
        <table class="table pc-tabla align-middle mb-0">
            <thead>
                <tr>
                    <th>Tarifa</th>
                    <th>Valor hora</th>
                    <th>Fracción</th>
                    <th>Valor fracción</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tarifas === []): ?>
                    <tr>
                        <td colspan="6">
                            <div class="pc-tabla-vacia">
                                <i class="bi bi-tags fs-3 d-block mb-2"></i>
                                No hay tarifas registradas.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($tarifas as $t): ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($t['nombre']); ?></td>
                        <td class="pc-monto"><?php echo e(dinero($t['valorHora'])); ?></td>
                        <td><?php echo (int) $t['fraccionMinutos']; ?> min</td>
                        <td><?php echo $t['valorFraccion'] !== null ? e(dinero($t['valorFraccion'])) : 'Calculado'; ?></td>
                        <td>
                            <?php if ((int) $t['activo'] === 1): ?>
                                <span class="pc-badge text-bg-success">Activa</span>
                            <?php else: ?>
                                <span class="pc-badge text-bg-secondary">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="<?php echo e(url('tarifas/editar', ['id' => $t['idTarifa']])); ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?php echo e(url('tarifas/estado')); ?>" class="d-inline"
                                      onsubmit="return confirm('¿<?php echo (int) $t['activo'] === 1 ? 'Desactivar' : 'Activar'; ?> la tarifa <?php echo e($t['nombre']); ?>?');">
                                    <?php echo Csrf::campo(); ?>
                                    <input type="hidden" name="idTarifa" value="<?php echo (int) $t['idTarifa']; ?>">
                                    <button type="submit" class="btn btn-sm <?php echo (int) $t['activo'] === 1 ? 'btn-outline-danger' : 'btn-outline-success'; ?>"
                                            title="<?php echo (int) $t['activo'] === 1 ? 'Desactivar' : 'Activar'; ?>">
                                        <i class="bi bi-<?php echo (int) $t['activo'] === 1 ? 'pause-circle' : 'play-circle'; ?>"></i>
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

<div class="alert alert-light border mt-3 small mb-0">
    <i class="bi bi-calculator me-1"></i>
    El cobro se calcula por bloques completos de la fracción configurada.
    Si no defines un valor por fracción, se prorratea el valor hora.
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
