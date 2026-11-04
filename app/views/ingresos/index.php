<?php
/**
 * ==========================================================================
 * ParkControl · app/views/ingresos/index.php
 * ==========================================================================
 * Listado de ingresos y registro en modal. El operador escribe los datos del
 * cliente y del vehículo; el sistema los crea o reutiliza y luego inserta el
 * ingreso.
 * ==========================================================================
 */

$titulo = $titulo ?? 'Ingresos';
$seccion = $seccion ?? 'ingresos';
require PC_APP . '/views/layout/header.php';

$valor = static fn (string $campo, mixed $defecto = '') => $old[$campo] ?? $defecto;
$csrf = Csrf::campo();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-0">Ingresos</h2>
        <p class="text-muted mb-0 small">Entradas, salidas y cobros del estacionamiento.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIngreso">
        <i class="bi bi-box-arrow-in-right me-1"></i> Registrar ingreso
    </button>
</div>

<div class="row g-3 mb-4">
    <?php
    $tarjetas = [
        ['Vehículos dentro', $resumen['abiertos'], 'car-front', 'azul'],
        ['Ingresos de hoy', $resumen['hoy'], 'box-arrow-in-right', 'verde'],
        ['Cobrado hoy', dinero($resumen['cobrado']), 'cash-coin', 'ambar'],
    ];
    foreach ($tarjetas as [$etiqueta, $valorTarjeta, $icono, $color]): ?>
        <div class="col-12 col-md-4">
            <div class="card pc-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="pc-card-icono <?php echo e($color); ?>"><i class="bi bi-<?php echo e($icono); ?>"></i></div>
                    <div>
                        <div class="pc-metrica-etiqueta"><?php echo e($etiqueta); ?></div>
                        <div class="pc-metrica-valor"><?php echo e($valorTarjeta); ?></div>
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
                    <?php foreach (['abierto' => 'Abiertos', 'cerrado' => 'Cerrados', 'anulado' => 'Anulados'] as $clave => $texto): ?>
                        <option value="<?php echo $clave; ?>" <?php echo $estado === $clave ? 'selected' : ''; ?>>
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
                       value="<?php echo e($busqueda); ?>" placeholder="Patente, cliente o fila">
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
                    <th>Fila</th>
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
                        <td><?php echo e($i['fila'] ?? '—'); ?></td>
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
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Anular"
                                            data-confirmar
                                            data-confirm-action="<?php echo e(url('ingresos/anular')); ?>"
                                            data-confirm-campo="idIngreso"
                                            data-confirm-valor="<?php echo (int) $i['idIngreso']; ?>"
                                            data-confirm-titulo="Anular ingreso"
                                            data-confirm-mensaje="Se anulará el ingreso de <?php echo e($i['patente']); ?> sin cobrar."
                                            data-confirm-aceptar="Anular">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
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

<!-- Modal: registrar ingreso -->
<div class="modal fade" id="modalIngreso" tabindex="-1" aria-labelledby="modalIngresoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(url('ingresos/crear')); ?>" novalidate>
                <?php echo $csrf; ?>

                <div class="modal-header">
                    <h5 class="modal-title" id="modalIngresoTitulo">Registrar ingreso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <?php if ($errores !== []): ?>
                        <div class="alert alert-danger small"><?php echo e(reset($errores)); ?></div>
                    <?php endif; ?>

                    <!-- 1. Datos del cliente -->
                    <section class="pc-jumbotron mb-3">
                        <h6 class="pc-jumbotron-titulo"><i class="bi bi-person-vcard me-1"></i> Datos del cliente</h6>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <label for="miRut" class="form-label">RUT <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="miRut" name="rut"
                                       placeholder="12.345.678-9" maxlength="13" required value="<?php echo e($valor('rut')); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miNombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="miNombres" name="nombres"
                                       maxlength="65" required value="<?php echo e($valor('nombres')); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miApellidoPaterno" class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="miApellidoPaterno" name="apellidoPaterno"
                                       maxlength="30" required value="<?php echo e($valor('apellidoPaterno')); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miApellidoMaterno" class="form-label">Apellido materno</label>
                                <input type="text" class="form-control" id="miApellidoMaterno" name="apellidoMaterno"
                                       maxlength="30" value="<?php echo e($valor('apellidoMaterno')); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miTelefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="miTelefono" name="telefono"
                                       maxlength="13" placeholder="+56912345678" value="<?php echo e($valor('telefono')); ?>">
                            </div>
                            <div class="col-6 col-md-5">
                                <label for="miCorreo" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="miCorreo" name="correo"
                                       maxlength="65" placeholder="cliente@correo.cl" value="<?php echo e($valor('correo')); ?>">
                            </div>
                            <div class="col-12 col-md-4 d-flex align-items-end">
                                <div class="form-text mb-0">
                                    Si el RUT ya existe, se reutiliza el cliente y se actualizan sus datos.
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Datos del vehículo -->
                    <section class="pc-jumbotron mb-3">
                        <h6 class="pc-jumbotron-titulo"><i class="bi bi-car-front me-1"></i> Datos del vehículo</h6>
                        <div class="row g-3">
                            <div class="col-6 col-md-2">
                                <label for="miPatente" class="form-label">Patente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" id="miPatente" name="patente"
                                       placeholder="BBBB12" maxlength="7" required value="<?php echo e($valor('patente')); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miMarca" class="form-label">Marca <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="miMarca" name="marca" list="listaMarcas"
                                       maxlength="12" required value="<?php echo e($valor('marca')); ?>">
                                <datalist id="listaMarcas">
                                    <?php foreach (Marca::todas() as $m): ?>
                                        <option value="<?php echo e($m['marca']); ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="miModelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="miModelo" name="modelo" list="listaModelos"
                                       maxlength="12" required value="<?php echo e($valor('modelo')); ?>">
                                <datalist id="listaModelos">
                                    <?php foreach (Modelo::todos() as $m): ?>
                                        <option value="<?php echo e($m['modelo']); ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="miTipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select" id="miTipo" name="idTipoVehiculo" required>
                                    <?php foreach ($tipos as $t): ?>
                                        <option value="<?php echo (int) $t['idTipoVehiculo']; ?>"
                                            <?php echo (int) $valor('idTipoVehiculo', 1) === (int) $t['idTipoVehiculo'] ? 'selected' : ''; ?>>
                                            <?php echo e($t['tipoVehiculo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-1">
                                <label for="miAnio" class="form-label">Año</label>
                                <input type="number" class="form-control" id="miAnio" name="anio"
                                       min="1950" max="<?php echo (int) date('Y') + 1; ?>" value="<?php echo e($valor('anio')); ?>">
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="miColor" class="form-label">Color</label>
                                <input type="text" class="form-control" id="miColor" name="color"
                                       maxlength="12" value="<?php echo e($valor('color')); ?>">
                            </div>
                        </div>
                    </section>

                    <!-- 3. Datos del ingreso -->
                    <section class="pc-jumbotron">
                        <h6 class="pc-jumbotron-titulo"><i class="bi bi-receipt me-1"></i> Datos del ingreso</h6>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <label for="miFila" class="form-label">Fila <span class="text-danger">*</span></label>
                                <select class="form-select" id="miFila" name="idFila" required>
                                    <option value="">Selecciona una fila</option>
                                    <?php foreach ($filas as $f): ?>
                                        <option value="<?php echo (int) $f['idFila']; ?>"
                                            <?php echo (int) $valor('idFila', 0) === (int) $f['idFila'] ? 'selected' : ''; ?>>
                                            Fila <?php echo e($f['fila']); ?> · <?php echo (int) $f['libres']; ?> libres
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miServicio" class="form-label">Servicio extra</label>
                                <select class="form-select" id="miServicio" name="idServicioExtra">
                                    <option value="0">Sin servicio</option>
                                    <?php foreach ($servicios as $s): ?>
                                        <option value="<?php echo (int) $s['idServicioExtra']; ?>"
                                            <?php echo (int) $valor('idServicioExtra', 0) === (int) $s['idServicioExtra'] ? 'selected' : ''; ?>>
                                            <?php echo e($s['servicioExtra']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miFechaInicio" class="form-label">Fecha inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="miFechaInicio" name="fechaInicio" required
                                       value="<?php echo e($valor('fechaInicio', date('Y-m-d'))); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miHoraInicio" class="form-label">Hora inicio <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="miHoraInicio" name="horaInicio" required
                                       value="<?php echo e($valor('horaInicio', date('H:i'))); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miFechaRegreso" class="form-label">Fecha regreso</label>
                                <input type="date" class="form-control" id="miFechaRegreso" name="fechaRegreso"
                                       value="<?php echo e($valor('fechaRegreso')); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miHoraRegreso" class="form-label">Hora regreso</label>
                                <input type="time" class="form-control" id="miHoraRegreso" name="horaRegreso"
                                       value="<?php echo e($valor('horaRegreso')); ?>">
                            </div>
                            <div class="col-6 col-md-3">
                                <label for="miVuelo" class="form-label">Número de vuelo</label>
                                <input type="text" class="form-control" id="miVuelo" name="numeroVuelo"
                                       maxlength="12" placeholder="Opcional" value="<?php echo e($valor('numeroVuelo')); ?>">
                            </div>
                            <div class="col-6 col-md-3 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="miLlaves" name="llaves" value="1"
                                        <?php echo (int) $valor('llaves', 0) === 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="miLlaves">Se retienen las llaves</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="miObservacion" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="miObservacion" name="observacion" rows="2"
                                          maxlength="500"><?php echo e($valor('observacion')); ?></textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Registrar ingreso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require PC_APP . '/views/layout/footer.php'; ?>
