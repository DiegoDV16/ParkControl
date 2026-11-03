<?php
/**
 * ==========================================================================
 * ParkControl · app/views/ingresos/salida.php
 * ==========================================================================
 * Detalle del ingreso y registro de salida con cobro.
 * Variables: $ingreso, $minutos, $monto.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Registrar salida';
$seccion = $seccion ?? 'ingresos';
require PC_APP . '/views/layout/header.php';

$abierto = $ingreso['estado'] === 'abierto';
$duracion = $abierto
    ? sprintf('%d h %02d min', intdiv($minutos, 60), $minutos % 60)
    : sprintf('%d h %02d min', intdiv($minutos, 60), $minutos % 60);
?>

<div class="mb-4">
    <a href="<?php echo e(url('ingresos')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver a ingresos
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0">
        <?php echo $abierto ? 'Registrar salida' : 'Detalle del ingreso'; ?>
        <span class="text-muted fs-6">#<?php echo (int) $ingreso['idIngreso']; ?></span>
    </h2>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card pc-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="display-6 fw-bold"><?php echo e($ingreso['patente']); ?></div>
                        <div class="text-muted"><?php echo e(nombreCompleto($ingreso)); ?> · <?php echo e($ingreso['clienteRut']); ?></div>
                    </div>
                    <span class="pc-badge <?php echo $abierto ? 'text-bg-success' : 'text-bg-secondary'; ?> fs-6">
                        <?php echo $abierto ? 'Dentro' : ($ingreso['estado'] === 'cerrado' ? 'Cerrado' : 'Anulado'); ?>
                    </span>
                </div>

                <dl class="row mb-0 small">
                    <dt class="col-sm-4 text-muted">Plaza</dt>
                    <dd class="col-sm-8"><?php echo e(($ingreso['fila'] ?? '—') . ' · ' . ($ingreso['plaza'] ?? '—')); ?></dd>

                    <dt class="col-sm-4 text-muted">Entrada</dt>
                    <dd class="col-sm-8">
                        <?php echo e(fechaCorta($ingreso['fechaInicio']) . ' ' . horaCorta($ingreso['horaInicio'])); ?>
                    </dd>

                    <dt class="col-sm-4 text-muted">Salida</dt>
                    <dd class="col-sm-8">
                        <?php echo $ingreso['fechaTermino'] ? e(fechaCorta($ingreso['fechaTermino']) . ' ' . horaCorta($ingreso['horaTermino'])) : 'Aún dentro'; ?>
                    </dd>

                    <dt class="col-sm-4 text-muted">Tiempo</dt>
                    <dd class="col-sm-8"><?php echo e($duracion); ?></dd>

                    <dt class="col-sm-4 text-muted">Tarifa</dt>
                    <dd class="col-sm-8"><?php echo e($ingreso['tarifaNombre'] ?? 'Sin tarifa'); ?></dd>

                    <?php if (!empty($ingreso['servicioExtra'])): ?>
                        <dt class="col-sm-4 text-muted">Servicio extra</dt>
                        <dd class="col-sm-8"><?php echo e($ingreso['servicioExtra'] . ' · ' . dinero($ingreso['servicioValor'])); ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($ingreso['numeroVuelo'])): ?>
                        <dt class="col-sm-4 text-muted">N° de vuelo</dt>
                        <dd class="col-sm-8"><?php echo e($ingreso['numeroVuelo']); ?></dd>
                    <?php endif; ?>

                    <dt class="col-sm-4 text-muted">Llaves</dt>
                    <dd class="col-sm-8"><?php echo (int) $ingreso['llaves'] === 1 ? 'Retenidas' : 'No'; ?></dd>

                    <dt class="col-sm-4 text-muted">Registrado por</dt>
                    <dd class="col-sm-8"><?php echo e(trim(($ingreso['usuarioNombres'] ?? '') . ' ' . ($ingreso['usuarioApellido'] ?? ''))); ?></dd>

                    <?php if (!empty($ingreso['observacion'])): ?>
                        <dt class="col-sm-4 text-muted">Observaciones</dt>
                        <dd class="col-sm-8"><?php echo e($ingreso['observacion']); ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <?php if ($abierto): ?>
            <div class="card pc-card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="pc-metrica-etiqueta">Total a cobrar</div>
                        <div class="display-6 fw-bold text-primary"><?php echo e(dinero($monto)); ?></div>
                        <div class="small text-muted"><?php echo e($duracion); ?> de estadía</div>
                    </div>

                    <form method="POST" action="<?php echo e(url('ingresos/cerrar')); ?>" novalidate>
                        <?php echo Csrf::campo(); ?>
                        <input type="hidden" name="idIngreso" value="<?php echo (int) $ingreso['idIngreso']; ?>">

                        <div class="mb-3">
                            <label for="metodo" class="form-label">Medio de pago</label>
                            <select class="form-select" id="metodo" name="metodo">
                                <option value="efectivo">Efectivo</option>
                                <option value="debito">Débito</option>
                                <option value="credito">Crédito</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>

                        <div class="mb-3" id="bloqueRecibido">
                            <label for="recibido" class="form-label">Monto recibido</label>
                            <input type="number" class="form-control" id="recibido" name="recibido"
                                   min="0" step="100" value="<?php echo (int) $monto; ?>">
                            <div class="form-text" id="textoVuelto">Vuelto: $0</div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-cash-coin me-1"></i> Cobrar y liberar plaza
                        </button>
                    </form>

                    <hr>

                    <form method="POST" action="<?php echo e(url('ingresos/anular')); ?>"
                          onsubmit="return confirm('¿Anular este ingreso sin cobrar?');">
                        <?php echo Csrf::campo(); ?>
                        <input type="hidden" name="idIngreso" value="<?php echo (int) $ingreso['idIngreso']; ?>">
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-1"></i> Anular ingreso
                        </button>
                    </form>
                </div>
            </div>

            <script>
                (function () {
                    const total = <?php echo (float) $monto; ?>;
                    const metodo = document.getElementById('metodo');
                    const recibido = document.getElementById('recibido');
                    const bloque = document.getElementById('bloqueRecibido');
                    const texto = document.getElementById('textoVuelto');

                    function actualizar() {
                        bloque.style.display = metodo.value === 'efectivo' ? '' : 'none';
                        const vuelto = Math.max(0, (parseFloat(recibido.value) || 0) - total);
                        texto.textContent = 'Vuelto: $' + vuelto.toLocaleString('es-CL');
                    }

                    metodo.addEventListener('change', actualizar);
                    recibido.addEventListener('input', actualizar);
                    actualizar();
                })();
            </script>
        <?php else: ?>
            <div class="card pc-card">
                <div class="card-body text-center">
                    <div class="pc-metrica-etiqueta">Monto cobrado</div>
                    <div class="display-6 fw-bold"><?php echo e(dinero($ingreso['montoTotal'])); ?></div>
                    <p class="text-muted small mb-0 mt-2">
                        Este ingreso ya está <?php echo e($ingreso['estado']); ?>.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
