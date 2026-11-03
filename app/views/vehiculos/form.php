<?php
/**
 * ==========================================================================
 * ParkControl · app/views/vehiculos/form.php
 * ==========================================================================
 * Alta y edición de vehículos con selects dependientes marca → modelo.
 * Variables: $modo, $vehiculo, $errores, $old, $clientes, $marcas, $modelos.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Vehículo';
$seccion = $seccion ?? 'vehiculos';
require PC_APP . '/views/layout/header.php';

$esEdicion = $modo === 'editar';
$valor = static function (string $campo, mixed $defecto = '') use ($old, $vehiculo): mixed {
    return $old[$campo] ?? $vehiculo[$campo] ?? $defecto;
};
$accion = $esEdicion ? url('vehiculos/actualizar') : url('vehiculos/crear');
$idModeloActual = (int) $valor('idModelo', 0);
?>

<div class="mb-4">
    <a href="<?php echo e(url('vehiculos')); ?>" class="small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
    <h2 class="h4 fw-bold mt-2 mb-0"><?php echo $esEdicion ? 'Editar vehículo' : 'Nuevo vehículo'; ?></h2>
</div>

<div class="card pc-card pc-form-card">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo e($accion); ?>" novalidate>
            <?php echo Csrf::campo(); ?>
            <?php if ($esEdicion): ?>
                <input type="hidden" name="idVehiculo" value="<?php echo (int) ($vehiculo['idVehiculo'] ?? 0); ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="patente" class="form-label">Patente <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase <?php echo isset($errores['patente']) ? 'is-invalid' : ''; ?>"
                           id="patente" name="patente" placeholder="BBBB12" maxlength="7" required
                           value="<?php echo e($valor('patente')); ?>">
                    <?php if (isset($errores['patente'])): ?><div class="invalid-feedback"><?php echo e($errores['patente']); ?></div><?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label for="idCliente" class="form-label">Propietario</label>
                    <select class="form-select" id="idCliente" name="idCliente">
                        <option value="0">Sin asignar</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo (int) $c['idCliente']; ?>"
                                <?php echo (int) $valor('idCliente', 0) === (int) $c['idCliente'] ? 'selected' : ''; ?>>
                                <?php echo e(nombreCompleto($c) . ' · ' . $c['rut']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="idMarca" class="form-label">Marca <span class="text-danger">*</span></label>
                    <select class="form-select" id="idMarca" required>
                        <option value="">Selecciona una marca</option>
                        <?php foreach ($marcas as $m): ?>
                            <option value="<?php echo (int) $m['idMarca']; ?>"><?php echo e($m['marca']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="idModelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                    <select class="form-select <?php echo isset($errores['idModelo']) ? 'is-invalid' : ''; ?>"
                            id="idModelo" name="idModelo" required>
                        <option value="">Selecciona un modelo</option>
                    </select>
                    <?php if (isset($errores['idModelo'])): ?><div class="invalid-feedback"><?php echo e($errores['idModelo']); ?></div><?php endif; ?>
                </div>

                <div class="col-6 col-md-6">
                    <label for="anio" class="form-label">Año</label>
                    <input type="number" class="form-control <?php echo isset($errores['anio']) ? 'is-invalid' : ''; ?>"
                           id="anio" name="anio" min="1950" max="<?php echo (int) date('Y') + 1; ?>"
                           value="<?php echo e($valor('anio')); ?>">
                    <?php if (isset($errores['anio'])): ?><div class="invalid-feedback"><?php echo e($errores['anio']); ?></div><?php endif; ?>
                </div>

                <div class="col-6 col-md-6">
                    <label for="color" class="form-label">Color</label>
                    <input type="text" class="form-control" id="color" name="color" maxlength="12"
                           value="<?php echo e($valor('color')); ?>">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?php echo $esEdicion ? 'Guardar cambios' : 'Registrar vehículo'; ?>
                </button>
                <a href="<?php echo e(url('vehiculos')); ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modelos = <?php echo json_encode($modelos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const selMarca = document.getElementById('idMarca');
    const selModelo = document.getElementById('idModelo');
    const modeloActual = <?php echo $idModeloActual; ?>;

    function modeloPorId(id) {
        return modelos.find(function (m) { return parseInt(m.idModelo, 10) === id; }) || null;
    }

    function pintarModelos(idMarca, seleccionar) {
        selModelo.innerHTML = '<option value="">Selecciona un modelo</option>';
        modelos
            .filter(function (m) { return String(m.idMarca) === String(idMarca); })
            .forEach(function (m) {
                const opcion = document.createElement('option');
                opcion.value = m.idModelo;
                opcion.textContent = m.modelo;
                if (seleccionar && parseInt(m.idModelo, 10) === seleccionar) {
                    opcion.selected = true;
                }
                selModelo.appendChild(opcion);
            });
    }

    selMarca.addEventListener('change', function () {
        pintarModelos(selMarca.value, 0);
    });

    const inicial = modeloPorId(modeloActual);
    if (inicial) {
        selMarca.value = inicial.idMarca;
        pintarModelos(inicial.idMarca, modeloActual);
    }
})();
</script>

<?php require PC_APP . '/views/layout/footer.php'; ?>
