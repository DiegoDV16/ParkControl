<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/ConfiguracionController.php
 * ==========================================================================
 * Parámetros del sistema y cambio de contraseña del usuario en sesión.
 * ==========================================================================
 */

class ConfiguracionController
{
    public function index(): void
    {
        $valores = Configuracion::todas();
        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);

        $titulo = 'Configuración';
        $seccion = 'configuracion';
        require PC_APP . '/views/configuracion/index.php';
    }

    public function guardar(): void
    {
        $datos = [
            'nombre_estacionamiento' => trim((string) ($_POST['nombre_estacionamiento'] ?? '')),
            'direccion'              => trim((string) ($_POST['direccion'] ?? '')),
            'telefono'               => trim((string) ($_POST['telefono'] ?? '')),
            'moneda'                 => trim((string) ($_POST['moneda'] ?? 'CLP')),
        ];

        $v = (new Validador($datos))
            ->requerido('nombre_estacionamiento', 'nombre')
            ->longitud('nombre_estacionamiento', 'nombre', 2, 100)
            ->longitud('direccion', 'dirección', 0, 150)
            ->longitud('telefono', 'teléfono', 0, 30)
            ->enLista('moneda', 'moneda', ['CLP', 'USD', 'EUR']);

        if (!$v->valido()) {
            $_SESSION['_errores'] = $v->errores();
            $_SESSION['_old'] = $datos;
            Flash::error('Revisa los datos de configuración.');
            redirigir('configuracion');
        }

        Configuracion::guardar($datos);
        Auditoria::registrar('actualizar', 'configuracion', null, 'Parámetros del sistema');
        Flash::exito('Configuración guardada.');
        redirigir('configuracion');
    }

    public function cambiarClave(): void
    {
        $actual = (string) ($_POST['clave_actual'] ?? '');
        $nueva = (string) ($_POST['clave_nueva'] ?? '');
        $confirmar = (string) ($_POST['clave_confirmar'] ?? '');

        $usuario = Usuario::porId((int) Auth::usuario()['id']);

        if ($usuario === null || !password_verify($actual, $usuario['contrasena'])) {
            Flash::error('La contraseña actual no es correcta.');
            redirigir('configuracion');
        }

        if (mb_strlen($nueva) < 8) {
            Flash::error('La nueva contraseña debe tener al menos 8 caracteres.');
            redirigir('configuracion');
        }

        if ($nueva !== $confirmar) {
            Flash::error('La confirmación no coincide con la nueva contraseña.');
            redirigir('configuracion');
        }

        Usuario::actualizarHash((int) $usuario['idUsuario'], password_hash($nueva, PASSWORD_DEFAULT));
        Auditoria::registrar('cambiarClave', 'usuario', (int) $usuario['idUsuario'], 'Cambio de contraseña propio');

        session_regenerate_id(true);
        Flash::exito('Contraseña actualizada correctamente.');
        redirigir('configuracion');
    }
}
