<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/IngresoController.php
 * ==========================================================================
 * Registro de ingresos en modal: se capturan los datos del cliente y del
 * vehículo (no se seleccionan) y se insertan junto al ingreso.
 * ==========================================================================
 */

class IngresoController
{
    public function index(): void
    {
        $estado = (string) ($_GET['estado'] ?? '');
        $busqueda = trim((string) ($_GET['q'] ?? ''));
        $desde = (string) ($_GET['desde'] ?? '');
        $hasta = (string) ($_GET['hasta'] ?? '');

        $ingresos = Ingreso::listar($estado, $busqueda, $desde, $hasta);
        $resumen = [
            'abiertos' => Ingreso::abiertos(),
            'hoy'      => Ingreso::hoy(),
            'cobrado'  => Ingreso::recaudacionHoy(),
        ];

        $filas = Fila::disponibles();
        $servicios = ServicioExtra::activos();
        $tipos = TipoVehiculo::todos();

        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        $abrirModal = $_SESSION['_modal_ingreso'] ?? false;
        unset($_SESSION['_errores'], $_SESSION['_old'], $_SESSION['_modal_ingreso']);

        $titulo = 'Ingresos';
        $seccion = 'ingresos';
        require PC_APP . '/views/ingresos/index.php';
    }

    public function guardar(): void
    {
        $datos = [
            // Cliente
            'rut'             => trim((string) ($_POST['rut'] ?? '')),
            'nombres'         => trim((string) ($_POST['nombres'] ?? '')),
            'apellidoPaterno' => trim((string) ($_POST['apellidoPaterno'] ?? '')),
            'apellidoMaterno' => trim((string) ($_POST['apellidoMaterno'] ?? '')),
            'telefono'        => trim((string) ($_POST['telefono'] ?? '')),
            'correo'          => trim((string) ($_POST['correo'] ?? '')),
            // Vehículo
            'patente'         => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim((string) ($_POST['patente'] ?? ''))) ?? ''),
            'marca'           => trim((string) ($_POST['marca'] ?? '')),
            'modelo'          => trim((string) ($_POST['modelo'] ?? '')),
            'anio'            => (int) ($_POST['anio'] ?? 0),
            'color'           => trim((string) ($_POST['color'] ?? '')),
            'idTipoVehiculo'  => (int) ($_POST['idTipoVehiculo'] ?? 0),
            // Ingreso
            'idFila'          => (int) ($_POST['idFila'] ?? 0),
            'idServicioExtra' => (int) ($_POST['idServicioExtra'] ?? 0),
            'fechaInicio'     => trim((string) ($_POST['fechaInicio'] ?? '')),
            'horaInicio'      => trim((string) ($_POST['horaInicio'] ?? '')),
            'fechaRegreso'    => trim((string) ($_POST['fechaRegreso'] ?? '')),
            'horaRegreso'     => trim((string) ($_POST['horaRegreso'] ?? '')),
            'numeroVuelo'     => trim((string) ($_POST['numeroVuelo'] ?? '')),
            'llaves'          => isset($_POST['llaves']) ? 1 : 0,
            'observacion'     => trim((string) ($_POST['observacion'] ?? '')),
        ];

        $v = (new Validador(array_map('strval', $datos)))
            ->requerido('rut', 'RUT')
            ->rut('rut', 'RUT')
            ->requerido('nombres', 'nombres')
            ->requerido('apellidoPaterno', 'apellido paterno')
            ->longitud('nombres', 'nombres', 2, 65)
            ->longitud('apellidoPaterno', 'apellido paterno', 2, 30)
            ->longitud('apellidoMaterno', 'apellido materno', 0, 30)
            ->longitud('telefono', 'teléfono', 0, 13)
            ->correo('correo', 'correo')
            ->requerido('patente', 'patente')
            ->patente('patente', 'patente')
            ->requerido('marca', 'marca')
            ->requerido('modelo', 'modelo')
            ->longitud('marca', 'marca', 2, 12)
            ->longitud('modelo', 'modelo', 1, 12)
            ->entero('anio', 'año', 1950, (int) date('Y') + 1)
            ->longitud('color', 'color', 0, 12)
            ->entero('idTipoVehiculo', 'tipo de vehículo', 1, PHP_INT_MAX)
            ->requerido('fechaInicio', 'fecha de inicio')
            ->requerido('horaInicio', 'hora de inicio')
            ->fecha('fechaInicio', 'fecha de inicio')
            ->fecha('fechaRegreso', 'fecha de regreso')
            ->longitud('numeroVuelo', 'número de vuelo', 0, 12)
            ->longitud('observacion', 'observación', 0, 500);

        if (!preg_match('/^\d{2}:\d{2}$/', $datos['horaInicio'])) {
            $v->agregar('horaInicio', 'La hora de inicio no es válida.');
        }
        if ($datos['horaRegreso'] !== '' && !preg_match('/^\d{2}:\d{2}$/', $datos['horaRegreso'])) {
            $v->agregar('horaRegreso', 'La hora de regreso no es válida.');
        }
        if ($datos['fechaRegreso'] !== '' && $datos['fechaRegreso'] < $datos['fechaInicio']) {
            $v->agregar('fechaRegreso', 'La fecha de regreso no puede ser anterior al inicio.');
        }

        if ($datos['idTipoVehiculo'] > 0 && TipoVehiculo::porId($datos['idTipoVehiculo']) === null) {
            $v->agregar('idTipoVehiculo', 'El tipo de vehículo no es válido.');
        }

        $fila = $datos['idFila'] > 0 ? Fila::porId($datos['idFila']) : null;
        if ($fila === null || (int) $fila['estado'] !== 1
            || Fila::ocupadas($datos['idFila']) >= (int) $fila['capacidadTotal']) {
            $v->agregar('idFila', 'La fila seleccionada no tiene espacio disponible.');
        }

        if ($datos['idServicioExtra'] > 0 && ServicioExtra::porId($datos['idServicioExtra']) === null) {
            $v->agregar('idServicioExtra', 'El servicio seleccionado no es válido.');
        }

        if (!$v->valido()) {
            $_SESSION['_errores'] = $v->errores();
            $_SESSION['_old'] = $datos;
            $_SESSION['_modal_ingreso'] = '#modalIngreso';
            Flash::error('Revisa los datos del ingreso: ' . reset($v->errores()));
            redirigir('ingresos');
        }

        try {
            $id = Ingreso::registrarConDatos([
                'rut'             => $datos['rut'],
                'nombres'         => $datos['nombres'],
                'apellidoPaterno' => $datos['apellidoPaterno'],
                'apellidoMaterno' => $datos['apellidoMaterno'],
                'telefono'        => $datos['telefono'],
                'correo'          => $datos['correo'],
                'patente'         => $datos['patente'],
                'marca'           => $datos['marca'],
                'modelo'          => $datos['modelo'],
                'anio'            => $datos['anio'],
                'color'           => $datos['color'],
                'idTipoVehiculo'  => $datos['idTipoVehiculo'],
                'idServicioExtra' => $datos['idServicioExtra'],
                'idUsuario'       => (int) Auth::usuario()['id'],
                'idFila'          => $datos['idFila'],
                'fechaInicio'     => $datos['fechaInicio'],
                'horaInicio'      => $datos['horaInicio'],
                'fechaRegreso'    => $datos['fechaRegreso'],
                'horaRegreso'     => $datos['horaRegreso'],
                'numeroVuelo'     => $datos['numeroVuelo'],
                'llaves'          => $datos['llaves'],
                'observacion'     => $datos['observacion'],
            ]);
        } catch (RuntimeException $error) {
            $_SESSION['_errores'] = ['general' => $error->getMessage()];
            $_SESSION['_old'] = $datos;
            $_SESSION['_modal_ingreso'] = '#modalIngreso';
            Flash::error($error->getMessage());
            redirigir('ingresos');
        }

        Auditoria::registrar('crear', 'ingreso', $id, 'Ingreso de ' . $datos['patente'] . ' en fila ' . $fila['fila']);
        Flash::exito('Ingreso #' . $id . ' registrado para ' . $datos['patente']
            . ' en la fila ' . $fila['fila'] . '.');
        redirigir('ingresos');
    }

    public function salida(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $ingreso = Ingreso::porId($id);

        if ($ingreso === null) {
            Flash::error('El ingreso solicitado no existe.');
            redirigir('ingresos');
        }

        $minutos = Ingreso::minutosTranscurridos($ingreso);
        $dias = Tarifa::diasCobrables($minutos);
        $monto = Ingreso::montoEstimado($ingreso, $minutos);

        $titulo = 'Registrar salida';
        $seccion = 'ingresos';
        require PC_APP . '/views/ingresos/salida.php';
    }

    public function cerrar(): void
    {
        $id = (int) ($_POST['idIngreso'] ?? 0);
        $ingreso = Ingreso::porId($id);

        if ($ingreso === null || $ingreso['fechaTermino'] !== null || $ingreso['estado'] !== 'abierto') {
            Flash::error('El ingreso no está abierto.');
            redirigir('ingresos');
        }

        $metodo = (string) ($_POST['metodo'] ?? 'efectivo');
        $recibido = (float) str_replace(['.', ','], ['', '.'], (string) ($_POST['recibido'] ?? '0'));

        $minutos = Ingreso::minutosTranscurridos($ingreso);
        $monto = Ingreso::montoEstimado($ingreso, $minutos);

        $v = (new Validador(['metodo' => $metodo]))
            ->enLista('metodo', 'medio de pago', ['efectivo', 'debito', 'credito', 'transferencia']);

        if ($metodo === 'efectivo' && $recibido < $monto) {
            $v->agregar('recibido', 'El monto recibido es menor que el total a pagar.');
        }

        if (!$v->valido()) {
            Flash::error($v->primerError() ?? 'Revisa los datos del pago.');
            redirigir('ingresos/salida', ['id' => $id]);
        }

        $vuelto = $metodo === 'efectivo' ? max(0, $recibido - $monto) : 0;

        try {
            Ingreso::cerrar($id, $monto, $metodo, $recibido, $vuelto);
        } catch (RuntimeException $error) {
            Flash::error($error->getMessage());
            redirigir('ingresos');
        }

        Auditoria::registrar('cerrar', 'ingreso', $id, 'Cobro de ' . dinero($monto) . ' (' . $metodo . ')');
        Flash::exito('Salida registrada. Total cobrado: ' . dinero($monto)
            . ($vuelto > 0 ? ' · Vuelto: ' . dinero($vuelto) : '') . '.');
        redirigir('ingresos');
    }

    public function anular(): void
    {
        $id = (int) ($_POST['idIngreso'] ?? 0);
        $ingreso = Ingreso::porId($id);

        if ($ingreso === null) {
            Flash::error('El ingreso solicitado no existe.');
            redirigir('ingresos');
        }

        try {
            Ingreso::anular($id);
        } catch (RuntimeException $error) {
            Flash::error($error->getMessage());
            redirigir('ingresos');
        }

        Auditoria::registrar('anular', 'ingreso', $id, 'Ingreso anulado de ' . $ingreso['patente']);
        Flash::advertencia('Ingreso anulado.');
        redirigir('ingresos');
    }
}
