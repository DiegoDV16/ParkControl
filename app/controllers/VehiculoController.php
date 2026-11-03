<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/VehiculoController.php
 * ==========================================================================
 * Gestión de vehículos y su asociación con un cliente.
 * ==========================================================================
 */

class VehiculoController
{
    public function index(): void
    {
        $busqueda = trim((string) ($_GET['q'] ?? ''));
        $vehiculos = Vehiculo::listar($busqueda);

        $titulo = 'Vehículos';
        $seccion = 'vehiculos';
        require PC_APP . '/views/vehiculos/index.php';
    }

    public function crear(): void
    {
        $vehiculo = [
            'idVehiculo' => 0,
            'idCliente'  => 0,
            'patente'    => '',
            'anio'       => '',
            'color'      => '',
            'idModelo'   => 0,
        ];
        $modo = 'crear';
        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);
        extract($this->catalogos(), EXTR_OVERWRITE);

        $titulo = 'Nuevo vehículo';
        $seccion = 'vehiculos';
        require PC_APP . '/views/vehiculos/form.php';
    }

    public function guardar(): void
    {
        $datos = $this->datosFormulario();
        $validacion = $this->validar($datos, 0);

        if (!$validacion->valido()) {
            $this->volverConErrores($validacion->errores(), $datos, 'vehiculos/crear');
        }

        $id = Vehiculo::crear($datos);
        Auditoria::registrar('crear', 'vehiculo', $id, 'Alta de vehículo ' . $datos['patente']);
        Flash::exito('Vehículo registrado correctamente.');
        redirigir('vehiculos');
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $vehiculo = Vehiculo::porId($id);
        if ($vehiculo === null) {
            Flash::error('El vehículo solicitado no existe.');
            redirigir('vehiculos');
        }

        $modo = 'editar';
        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);
        extract($this->catalogos(), EXTR_OVERWRITE);

        $titulo = 'Editar vehículo';
        $seccion = 'vehiculos';
        require PC_APP . '/views/vehiculos/form.php';
    }

    public function actualizar(): void
    {
        $id = (int) ($_POST['idVehiculo'] ?? 0);
        if (Vehiculo::porId($id) === null) {
            Flash::error('El vehículo solicitado no existe.');
            redirigir('vehiculos');
        }

        $datos = $this->datosFormulario();
        $validacion = $this->validar($datos, $id);

        if (!$validacion->valido()) {
            $this->volverConErrores($validacion->errores(), $datos, 'vehiculos/editar', ['id' => $id]);
        }

        Vehiculo::actualizar($id, $datos);
        Auditoria::registrar('actualizar', 'vehiculo', $id, 'Edición de vehículo ' . $datos['patente']);
        Flash::exito('Vehículo actualizado correctamente.');
        redirigir('vehiculos');
    }

    public function eliminar(): void
    {
        $id = (int) ($_POST['idVehiculo'] ?? 0);
        $vehiculo = Vehiculo::porId($id);

        if ($vehiculo === null) {
            Flash::error('El vehículo solicitado no existe.');
            redirigir('vehiculos');
        }

        if (Vehiculo::tieneIngresos($id)) {
            Flash::error('No se puede eliminar: el vehículo tiene ingresos registrados.');
            redirigir('vehiculos');
        }

        Vehiculo::eliminar($id);
        Auditoria::registrar('eliminar', 'vehiculo', $id, $vehiculo['patente']);
        Flash::exito('Vehículo eliminado.');
        redirigir('vehiculos');
    }

    /** Catálogos para los selects del formulario. */
    private function catalogos(): array
    {
        return [
            'clientes' => Cliente::activos(),
            'marcas'   => Marca::todas(),
            'modelos'  => Modelo::todos(),
        ];
    }

    /** @return array<string, mixed> */
    private function datosFormulario(): array
    {
        return [
            'idCliente' => (int) ($_POST['idCliente'] ?? 0),
            'patente'   => strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim((string) ($_POST['patente'] ?? ''))) ?? ''),
            'anio'      => (int) ($_POST['anio'] ?? 0),
            'color'     => trim((string) ($_POST['color'] ?? '')),
            'idModelo'  => (int) ($_POST['idModelo'] ?? 0),
        ];
    }

    private function validar(array $datos, int $idActual): Validador
    {
        $v = (new Validador($datos))
            ->requerido('patente', 'patente')
            ->patente('patente', 'patente')
            ->entero('anio', 'año', 1950, (int) date('Y') + 1)
            ->longitud('color', 'color', 0, 12)
            ->entero('idModelo', 'modelo', 1, PHP_INT_MAX);

        if ($datos['patente'] !== '' && Vehiculo::existePatente($datos['patente'], $idActual)) {
            $v->agregar('patente', 'Ya existe un vehículo con esa patente.');
        }

        if ($datos['idModelo'] > 0 && !Modelo::existe($datos['idModelo'])) {
            $v->agregar('idModelo', 'El modelo seleccionado no es válido.');
        }

        if ($datos['idCliente'] > 0 && Cliente::porId($datos['idCliente']) === null) {
            $v->agregar('idCliente', 'El cliente seleccionado no es válido.');
        }

        return $v;
    }

    private function volverConErrores(array $errores, array $datos, string $ruta, array $parametros = []): never
    {
        $_SESSION['_errores'] = $errores;
        $_SESSION['_old'] = $datos;
        Flash::error('Revisa los campos marcados en el formulario.');
        redirigir($ruta, $parametros);
    }
}
