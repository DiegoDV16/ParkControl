<?php
/**
 * ==========================================================================
 * ParkControl · app/views/ingresos/form.php
 * ==========================================================================
 * Registro de entrada: cliente, vehículo, fila y datos opcionales.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Nuevo ingreso';
$seccion = $seccion ?? 'ingresos';
require PC_APP . '/views/layout/header.php';

$errores = $errores ?? [];
$old = $old ?? [];
$vehiculosJson = [];
foreach ($vehiculos as $v) {
    $vehiculosJson[] = [
        'idVehiculo' => (int) $v['idVehiculo'],
        'idCliente'  => (int) ($v['idCliente'] ?? 0),
        'etiqueta'   => $v['patente'] . ' · ' . $v['marca'] . ' ' . $v['modelo'],
    ];
}
?>

<div class="mb-4">
    <a href="<?php echo e(url('ingresos')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver a ingresos
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0">Registrar ingreso</h2>
</div>

<?php if ($filas === []): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Todas las filas están completas. Registra una salida para liberar espacio.
    </div>
<?php endif; ?>

<div class="card pc-card pc-form-card">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo e(url('ingresos/crear')); ?>" novalidate>
            <?php echo Csrf::campo(); ?>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="idCliente" class="form-label">Cliente <span class="text-danger">*</span></label>
                    <select class="form-select <?php echo isset($errores['idCliente']) ? 'is-invalid' : ''; ?>"
                            id="idCliente" name="idCliente" required>
                        <option value="">Selecciona un cliente</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo (int) $c['idCliente']; ?>"
                                <?php echo (int) ($old['idCliente'] ?? 0) === (int) $c['idCliente'] ? 'selected' : ''; ?>>
                                <?php echo e(nombreCompleto($c) . ' · ' . $c['rut']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['idCliente'])): ?><div class="invalid-feedback"><?php echo e($errores['idCliente']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="idVehiculo" class="form-label">Vehículo <span class="text-danger">*</span></label>
                    <select class="form-select <?php echo isset($errores['idVehiculo']) ? 'is-invalid' : ''; ?>"
                            id="idVehiculo" name="idVehiculo" required>
                        <option value="">Selecciona un vehículo</option>
                    </select>
                    <?php if (isset($errores['idVehiculo'])): ?><div class="invalid-feedback"><?php echo e($errores['idVehiculo']); ?></div><?php endif; ?>
                    <div class="form-text">Solo se listan vehículos del cliente seleccionado.</div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="idFila" class="form-label">Fila <span class="text-danger">*</span></label>
                    <select class="form-select <?php echo isset($errores['idFila']) ? 'is-invalid' : ''; ?>"
                            id="idFila" name="idFila" required>
                        <option value="">Selecciona una fila</option>
                        <?php foreach ($filas as $f): ?>
                            <option value="<?php echo (int) $f['idFila']; ?>"
                                <?php echo (int) ($old['idFila'] ?? 0) === (int) $f['idFila'] ? 'selected' : ''; ?>>
                                Fila <?php echo e($f['fila']); ?> · <?php echo (int) $f['libres']; ?> espacios libres
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errores['idFila'])): ?><div class="invalid-feedback"><?php echo e($errores['idFila']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="idServicioExtra" class="form-label">Servicio extra</label>
                    <select class="form-select" id="idServicioExtra" name="idServicioExtra">
                        <?php foreach ($servicios as $s): ?>
                            <option value="<?php echo (int) $s['idServicioExtra']; ?>"
                                <?php echo (int) ($old['idServicioExtra'] ?? 0) === (int) $s['idServicioExtra'] ? 'selected' : ''; ?>>
                                <?php echo e($s['servicioExtra']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">El precio depende del tipo de vehículo (ver Tarifas).</div>
                </div>

                <div class="col-6 col-md-6">
                    <label for="fechaRegreso" class="form-label">Fecha de regreso</label>
                    <input type="date" class="form-control <?php echo isset($errores['fechaRegreso']) ? 'is-invalid' : ''; ?>"
                           id="fechaRegreso" name="fechaRegreso" min="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo e($old['fechaRegreso'] ?? ''); ?>">
                    <?php if (isset($errores['fechaRegreso'])): ?><div class="invalid-feedback"><?php echo e($errores['fechaRegreso']); ?></div><?php endif; ?>
                </div>

                <div class="col-6 col-md-6">
                    <label for="horaRegreso" class="form-label">Hora de regreso</label>
                    <input type="time" class="form-control" id="horaRegreso" name="horaRegreso"
                           value="<?php echo e($old['horaRegreso'] ?? ''); ?>">
                </div>

                <div class="col-12 col-md-6">
                    <label for="numeroVuelo" class="form-label">Número de vuelo</label>
                    <input type="text" class="form-control" id="numeroVuelo" name="numeroVuelo"
                           maxlength="12" placeholder="Opcional" value="<?php echo e($old['numeroVuelo'] ?? ''); ?>">
                </div>

                <div class="col-12 col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="llaves" name="llaves" value="1"
                            <?php echo !empty($old['llaves']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="llaves">Se retienen las llaves del vehículo</label>
                    </div>
                </div>

                <div class="col-12">
                    <label for="observacion" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observacion" name="observacion" rows="2"
                              maxlength="500"><?php echo e($old['observacion'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary" <?php echo $filas === [] ? 'disabled' : ''; ?>>
                    <i class="bi bi-check-lg me-1"></i> Registrar ingreso
                </button>
                <a href="<?php echo e(url('ingresos')); ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const vehiculos = <?php echo json_encode($vehiculosJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const selCliente = document.getElementById('idCliente');
    const selVehiculo = document.getElementById('idVehiculo');
    const seleccionado = <?php echo (int) ($old['idVehiculo'] ?? 0); ?>;

    function pintar(idCliente) {
        selVehiculo.innerHTML = '<option value="">Selecciona un vehículo</option>';
        vehiculos
            .filter(function (v) { return String(v.idCliente) === String(idCliente); })
            .forEach(function (v) {
                const o = document.createElement('option');
                o.value = v.idVehiculo;
                o.textContent = v.etiqueta;
                if (v.idVehiculo === seleccionado) {
                    o.selected = true;
                }
                selVehiculo.appendChild(o);
            });
    }

    selCliente.addEventListener('change', function () { pintar(selCliente.value); });
    if (selCliente.value) {
        pintar(selCliente.value);
    }
})();
</script>

<?php require PC_APP . '/views/layout/footer.php'; ?>
