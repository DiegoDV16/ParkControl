<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/UsuarioController.php
 * ==========================================================================
 * Gestión de usuarios mediante modales sobre el listado.
 * ==========================================================================
 */

class UsuarioController
{
    public function index(): void
    {
        $busqueda = trim((string) ($_GET['q'] ?? ''));
        $usuarios = Usuario::listar($busqueda);
        $roles = Rol::todas();

        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        $abrirModal = $_SESSION['_modal_usuario'] ?? false;
        unset($_SESSION['_errores'], $_SESSION['_old'], $_SESSION['_modal_usuario']);

        $titulo = 'Usuarios';
        $seccion = 'usuarios';
        require PC_APP . '/views/usuarios/index.php';
    }

    public function guardar(): void
    {
        $datos = $this->datosFormulario();
        $validacion = $this->validar($datos, 0);

        if (!$validacion->valido()) {
            $this->volverConErrores($validacion->errores(), $datos);
        }

        $id = Usuario::crear([
            'idRol'           => $datos['idRol'],
            'rut'             => Rut::formatear($datos['rut']),
            'nombres'         => $datos['nombres'],
            'apellidoPaterno' => $datos['apellidoPaterno'],
            'apellidoMaterno' => $datos['apellidoMaterno'],
            'correo'          => $datos['correo'],
            'contrasena'      => password_hash($datos['contrasena'], PASSWORD_DEFAULT),
        ]);

        Auditoria::registrar('crear', 'usuario', $id, 'Alta de usuario ' . $datos['correo']);
        Flash::exito('Usuario creado correctamente.');
        redirigir('usuarios');
    }

    public function actualizar(): void
    {
        $id = (int) ($_POST['idUsuario'] ?? 0);
        $usuario = Usuario::porId($id);
        if ($usuario === null) {
            Flash::error('El usuario solicitado no existe.');
            redirigir('usuarios');
        }

        $datos = $this->datosFormulario();
        $validacion = $this->validar($datos, $id);

        if (!$validacion->valido()) {
            $this->volverConErrores($validacion->errores(), $datos + ['idUsuario' => (string) $id]);
        }

        if ($id === (int) Auth::usuario()['id'] && $datos['idRol'] !== 1
            && Usuario::contarAdministradoresActivos($id) === 0) {
            Flash::error('No puedes quitarte el rol de administrador: eres el único activo.');
            redirigir('usuarios');
        }

        Usuario::actualizar($id, [
            'idRol'           => $datos['idRol'],
            'rut'             => Rut::formatear($datos['rut']),
            'nombres'         => $datos['nombres'],
            'apellidoPaterno' => $datos['apellidoPaterno'],
            'apellidoMaterno' => $datos['apellidoMaterno'],
            'correo'          => $datos['correo'],
        ]);

        Auditoria::registrar('actualizar', 'usuario', $id, 'Edición de usuario ' . $datos['correo']);
        Flash::exito('Usuario actualizado correctamente.');
        redirigir('usuarios');
    }

    public function estado(): void
    {
        $id = (int) ($_POST['idUsuario'] ?? 0);
        $usuario = Usuario::porId($id);

        if ($usuario === null) {
            Flash::error('El usuario solicitado no existe.');
            redirigir('usuarios');
        }

        if ($id === (int) Auth::usuario()['id']) {
            Flash::error('No puedes desactivar tu propia cuenta.');
            redirigir('usuarios');
        }

        $nuevoEstado = (int) $usuario['estado'] === 1 ? 0 : 1;

        if ($nuevoEstado === 0 && $usuario['rolNombre'] === 'Administrador'
            && Usuario::contarAdministradoresActivos($id) === 0) {
            Flash::error('Debe existir al menos un administrador activo.');
            redirigir('usuarios');
        }

        Usuario::cambiarEstado($id, $nuevoEstado);
        Auditoria::registrar(
            $nuevoEstado === 1 ? 'activar' : 'desactivar',
            'usuario',
            $id,
            $usuario['correo']
        );

        Flash::exito($nuevoEstado === 1 ? 'Usuario activado.' : 'Usuario desactivado.');
        redirigir('usuarios');
    }

    public function restablecer(): void
    {
        $id = (int) ($_POST['idUsuario'] ?? 0);
        $usuario = Usuario::porId($id);

        if ($usuario === null) {
            Flash::error('El usuario solicitado no existe.');
            redirigir('usuarios');
        }

        $temporal = bin2hex(random_bytes(5));
        Usuario::actualizarHash($id, password_hash($temporal, PASSWORD_DEFAULT));
        Auditoria::registrar('restablecer', 'usuario', $id, 'Contraseña restablecida');

        Flash::advertencia(
            'Contraseña temporal de ' . $usuario['correo'] . ': ' . $temporal
            . ' — comunícala al usuario y pídele cambiarla.'
        );
        redirigir('usuarios');
    }

    /** @return array<string, string> */
    private function datosFormulario(): array
    {
        return [
            'idRol'           => (string) ($_POST['idRol'] ?? ''),
            'rut'             => trim((string) ($_POST['rut'] ?? '')),
            'nombres'         => trim((string) ($_POST['nombres'] ?? '')),
            'apellidoPaterno' => trim((string) ($_POST['apellidoPaterno'] ?? '')),
            'apellidoMaterno' => trim((string) ($_POST['apellidoMaterno'] ?? '')),
            'correo'          => trim((string) ($_POST['correo'] ?? '')),
            'contrasena'      => (string) ($_POST['contrasena'] ?? ''),
        ];
    }

    private function validar(array $datos, int $idActual): Validador
    {
        $v = (new Validador($datos))
            ->requerido('nombres', 'nombres')
            ->requerido('apellidoPaterno', 'apellido paterno')
            ->requerido('correo', 'correo')
            ->requerido('rut', 'RUT')
            ->correo('correo', 'correo')
            ->rut('rut', 'RUT')
            ->longitud('nombres', 'nombres', 2, 65)
            ->longitud('apellidoPaterno', 'apellido paterno', 2, 30)
            ->longitud('apellidoMaterno', 'apellido materno', 0, 30)
            ->entero('idRol', 'rol', 1, 99);

        $idRol = (int) $datos['idRol'];
        if ($idRol > 0 && !Rol::existe($idRol)) {
            $v->agregar('idRol', 'El rol seleccionado no es válido.');
        }

        if ($idActual === 0 && mb_strlen($datos['contrasena']) < 8) {
            $v->agregar('contrasena', 'La contraseña debe tener al menos 8 caracteres.');
        }

        if ($datos['correo'] !== '' && Usuario::existeCorreo($datos['correo'], $idActual)) {
            $v->agregar('correo', 'Ya existe un usuario con ese correo.');
        }

        if ($datos['rut'] !== '' && Rut::valido($datos['rut'])
            && Usuario::existeRut(Rut::formatear($datos['rut']), $idActual)) {
            $v->agregar('rut', 'Ya existe un usuario con ese RUT.');
        }

        return $v;
    }

    private function volverConErrores(array $errores, array $datos): never
    {
        $_SESSION['_errores'] = $errores;
        $_SESSION['_old'] = $datos;
        $_SESSION['_modal_usuario'] = '#modalUsuario';
        Flash::error('Revisa los datos del usuario: ' . reset($errores));
        redirigir('usuarios');
    }
}
