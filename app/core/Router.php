<?php
/**
 * ==========================================================================
 * ParkControl · app/core/Router.php
 * ==========================================================================
 * Enrutador mínimo del front controller. Cada ruta declara método HTTP,
 * acción (controlador + método) y opciones de acceso:
 *
 *   $router->get('usuarios', [UsuarioController::class, 'index'], ['rol' => 1]);
 * ==========================================================================
 */

class Router
{
    /** @var array<string, array{accion: array{0: class-string, 1: string}, opciones: array}> */
    private array $rutas = [];

    public function get(string $ruta, array $accion, array $opciones = []): void
    {
        $this->registrar('GET', $ruta, $accion, $opciones);
    }

    public function post(string $ruta, array $accion, array $opciones = []): void
    {
        $this->registrar('POST', $ruta, $accion, $opciones);
    }

    private function registrar(string $metodo, string $ruta, array $accion, array $opciones): void
    {
        $this->rutas[$metodo . ' ' . trim($ruta, '/')] = [
            'accion'   => $accion,
            'opciones' => $opciones,
        ];
    }

    public function despachar(): void
    {
        $metodo = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $ruta = trim((string) ($_GET['ruta'] ?? 'dashboard'), '/');
        if ($ruta === '') {
            $ruta = 'dashboard';
        }

        $definicion = $this->rutas[$metodo . ' ' . $ruta] ?? null;
        if ($definicion === null) {
            $this->responderError(404, 'La página que buscas no existe.');
            return;
        }

        $opciones = $definicion['opciones'];
        $requiereSesion = $opciones['auth'] ?? true;

        if ($requiereSesion && !Auth::verificar()) {
            redirigir('login');
        }

        $rolRequerido = $opciones['rol'] ?? null;
        if ($rolRequerido !== null && !Auth::verificar()) {
            redirigir('login');
        }
        if ($rolRequerido !== null && Auth::rol() !== (int) $rolRequerido) {
            $this->responderError(403, 'No tienes permisos para acceder a esta sección.');
            return;
        }

        if ($metodo === 'POST' && !Csrf::validar()) {
            Flash::error('La sesión expiró o la solicitud no es válida. Inténtalo de nuevo.');
            redirigir($ruta);
        }

        [$clase, $accion] = $definicion['accion'];
        (new $clase())->{$accion}();
    }

    private function responderError(int $codigo, string $mensaje): void
    {
        http_response_code($codigo);
        $titulo = $codigo === 404 ? 'Página no encontrada' : 'Acceso denegado';
        require PC_APP . '/views/errores/index.php';
    }
}
