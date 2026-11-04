<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/ClienteController.php
 * ==========================================================================
 * Gestión de clientes mediante modales sobre el listado.
 * ==========================================================================
 */

class ClienteController
{
    public function index(): void
    {
        $busqueda = trim((string) ($_GET['q'] ?? ''));
        $clientes = Cliente::listar($busqueda);

        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        $abrirModal = $_SESSION['_modal_cliente'] ?? false;
        unset($_SESSION['_errores'], $_SESSION['_old'], $_SESSION['_modal_cliente']);

        $titulo = 'Clientes';
        $seccion = 'clientes';
        require PC_APP . '/views/clientes/index.php';
    }

    public function guardar(): void
    {
        $datos = $this->datosFormulario();
        $validacion = $this->validar($datos, 0);

        if (!$validacion->valido()) {
            $this->volverConErrores($validacion->errores(), $datos);
        }

        $id = Cliente::crear([
            'rut'             => Rut::formatear($datos['rut']),
            'nombres'         => $datos['nombres'],
            'apellidoPaterno' => $datos['apellidoPaterno'],
            'apellidoMaterno' => $datos['apellidoMaterno'],
            'telefono'        => $datos['telefono'],
            'correo'          => $datos['correo'],
        ]);

        Auditoria::registrar('crear', 'cliente', $id, 'Alta de cliente ' . $datos['rut']);
        Flash::exito('Cliente registrado correctamente.');
        redirigir('clientes');
    }

    public function actualizar(): void
    {
        $id = (int) ($_POST['idCliente'] ?? 0);
        if (Cliente::porId($id) === null) {
            Flash::error('El cliente solicitado no existe.');
            redirigir('clientes');
        }

        $datos = $this->datosFormulario();
        $validacion = $this->validar($datos, $id);

        if (!$validacion->valido()) {
            $this->volverConErrores($validacion->errores(), $datos + ['idCliente' => (string) $id]);
        }

        Cliente::actualizar($id, [
            'rut'             => Rut::formatear($datos['rut']),
            'nombres'         => $datos['nombres'],
            'apellidoPaterno' => $datos['apellidoPaterno'],
            'apellidoMaterno' => $datos['apellidoMaterno'],
            'telefono'        => $datos['telefono'],
            'correo'          => $datos['correo'],
        ]);

        Auditoria::registrar('actualizar', 'cliente', $id, 'Edición de cliente ' . $datos['rut']);
        Flash::exito('Cliente actualizado correctamente.');
        redirigir('clientes');
    }

    public function estado(): void
    {
        $id = (int) ($_POST['idCliente'] ?? 0);
        $cliente = Cliente::porId($id);

        if ($cliente === null) {
            Flash::error('El cliente solicitado no existe.');
            redirigir('clientes');
        }

        $nuevoEstado = (int) $cliente['estado'] === 1 ? 0 : 1;
        Cliente::cambiarEstado($id, $nuevoEstado);
        Auditoria::registrar(
            $nuevoEstado === 1 ? 'activar' : 'desactivar',
            'cliente',
            $id,
            $cliente['rut']
        );

        Flash::exito($nuevoEstado === 1 ? 'Cliente activado.' : 'Cliente desactivado.');
        redirigir('clientes');
    }

    /** @return array<string, string> */
    private function datosFormulario(): array
    {
        return [
            'rut'             => trim((string) ($_POST['rut'] ?? '')),
            'nombres'         => trim((string) ($_POST['nombres'] ?? '')),
            'apellidoPaterno' => trim((string) ($_POST['apellidoPaterno'] ?? '')),
            'apellidoMaterno' => trim((string) ($_POST['apellidoMaterno'] ?? '')),
            'telefono'        => trim((string) ($_POST['telefono'] ?? '')),
            'correo'          => trim((string) ($_POST['correo'] ?? '')),
        ];
    }

    private function validar(array $datos, int $idActual): Validador
    {
        $v = (new Validador($datos))
            ->requerido('nombres', 'nombres')
            ->requerido('apellidoPaterno', 'apellido paterno')
            ->requerido('rut', 'RUT')
            ->rut('rut', 'RUT')
            ->correo('correo', 'correo')
            ->longitud('nombres', 'nombres', 2, 65)
            ->longitud('apellidoPaterno', 'apellido paterno', 2, 30)
            ->longitud('apellidoMaterno', 'apellido materno', 0, 30)
            ->longitud('telefono', 'teléfono', 0, 13)
            ->longitud('correo', 'correo', 0, 65);

        if ($datos['rut'] !== '' && Rut::valido($datos['rut'])
            && Cliente::existeRut(Rut::formatear($datos['rut']), $idActual)) {
            $v->agregar('rut', 'Ya existe un cliente con ese RUT.');
        }

        return $v;
    }

    private function volverConErrores(array $errores, array $datos): never
    {
        $_SESSION['_errores'] = $errores;
        $_SESSION['_old'] = $datos;
        $_SESSION['_modal_cliente'] = '#modalCliente';
        Flash::error('Revisa los datos del cliente: ' . reset($errores));
        redirigir('clientes');
    }
}
