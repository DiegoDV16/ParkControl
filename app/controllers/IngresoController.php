<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/IngresoController.php
 * ==========================================================================
 * Registro de entradas, cálculo de estadía y cierre con cobro.
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

        $titulo = 'Ingresos';
        $seccion = 'ingresos';
        require PC_APP . '/views/ingresos/index.php';
    }

    public function crear(): void
    {
        $clientes = Cliente::activos();
        $vehiculos = Vehiculo::listar();
        $plazas = Plaza::libres();
        $tarifas = Tarifa::activas();
        $servicios = ServicioExtra::activos();
        $tarifaDefecto = Tarifa::predeterminada();

        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);

        $titulo = 'Nuevo ingreso';
        $seccion = 'ingresos';
        require PC_APP . '/views/ingresos/form.php';
    }

    public function guardar(): void
    {
        $datos = [
            'idCliente'       => (int) ($_POST['idCliente'] ?? 0),
            'idVehiculo'      => (int) ($_POST['idVehiculo'] ?? 0),
            'idPlaza'         => (int) ($_POST['idPlaza'] ?? 0),
            'idTarifa'        => (int) ($_POST['idTarifa'] ?? 0),
            'idServicioExtra' => (int) ($_POST['idServicioExtra'] ?? 0),
            'numeroVuelo'     => trim((string) ($_POST['numeroVuelo'] ?? '')),
            'llaves'          => isset($_POST['llaves']) ? 1 : 0,
            'observacion'     => trim((string) ($_POST['observacion'] ?? '')),
        ];

        $v = (new Validador(array_map('strval', $datos)))
            ->entero('idCliente', 'cliente', 1, PHP_INT_MAX)
            ->entero('idVehiculo', 'vehículo', 1, PHP_INT_MAX)
            ->entero('idPlaza', 'plaza', 1, PHP_INT_MAX)
            ->entero('idTarifa', 'tarifa', 1, PHP_INT_MAX)
            ->longitud('numeroVuelo', 'número de vuelo', 0, 12)
            ->longitud('observacion', 'observación', 0, 1000);

        $vehiculo = $datos['idVehiculo'] > 0 ? Vehiculo::porId($datos['idVehiculo']) : null;
        $plaza = $datos['idPlaza'] > 0 ? Plaza::porId($datos['idPlaza']) : null;
        $tarifa = $datos['idTarifa'] > 0 ? Tarifa::porId($datos['idTarifa']) : null;

        if ($vehiculo === null) {
            $v->agregar('idVehiculo', 'Selecciona un vehículo válido.');
        }
        if ($plaza === null || (int) $plaza['estado'] !== Plaza::LIBRE) {
            $v->agregar('idPlaza', 'La plaza seleccionada no está libre.');
        }
        if ($tarifa === null || (int) $tarifa['activo'] !== 1) {
            $v->agregar('idTarifa', 'Selecciona una tarifa activa.');
        }
        if ($datos['idCliente'] > 0 && Cliente::porId($datos['idCliente']) === null) {
            $v->agregar('idCliente', 'El cliente seleccionado no es válido.');
        }
        if ($vehiculo !== null && (int) $vehiculo['idCliente'] !== $datos['idCliente']) {
            $v->agregar('idCliente', 'El vehículo no pertenece al cliente seleccionado.');
        }
        if ($datos['idVehiculo'] > 0 && Ingreso::activoPorVehiculo($datos['idVehiculo']) !== null) {
            $v->agregar('idVehiculo', 'El vehículo ya tiene un ingreso abierto.');
        }

        if (!$v->valido()) {
            $_SESSION['_errores'] = $v->errores();
            $_SESSION['_old'] = $datos;
            Flash::error('Revisa los datos del ingreso.');
            redirigir('ingresos/crear');
        }

        try {
            $id = Ingreso::crear([
                'idServicioExtra' => $datos['idServicioExtra'],
                'idUsuario'       => (int) Auth::usuario()['id'],
                'idVehiculo'      => $datos['idVehiculo'],
                'idFila'          => (int) $plaza['idFila'],
                'idCliente'       => $datos['idCliente'],
                'idPlaza'         => $datos['idPlaza'],
                'idTarifa'        => $datos['idTarifa'],
                'numeroVuelo'     => $datos['numeroVuelo'],
                'llaves'          => $datos['llaves'],
                'observacion'     => $datos['observacion'],
            ]);
        } catch (RuntimeException $error) {
            Flash::error($error->getMessage());
            redirigir('ingresos/crear');
        }

        Auditoria::registrar('crear', 'ingreso', $id, 'Ingreso de ' . $vehiculo['patente']);
        Flash::exito('Ingreso registrado para la patente ' . $vehiculo['patente'] . '.');
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
        $monto = Ingreso::montoEstimado($ingreso, $minutos);

        $titulo = 'Registrar salida';
        $seccion = 'ingresos';
        require PC_APP . '/views/ingresos/salida.php';
    }

    public function cerrar(): void
    {
        $id = (int) ($_POST['idIngreso'] ?? 0);
        $ingreso = Ingreso::porId($id);

        if ($ingreso === null || $ingreso['estado'] !== 'abierto') {
            Flash::error('El ingreso no está abierto.');
            redirigir('ingresos');
        }

        $metodo = (string) ($_POST['metodo'] ?? 'efectivo');
        $recibido = (float) str_replace(['.', ','], ['', '.'], (string) ($_POST['recibido'] ?? '0'));

        $minutos = Ingreso::minutosTranscurridos($ingreso);
        $monto = Ingreso::montoEstimado($ingreso, $minutos);

        $v = (new Validador(['metodo' => $metodo, 'monto' => (string) $monto]))
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
        Flash::advertencia('Ingreso anulado y plaza liberada.');
        redirigir('ingresos');
    }
}
