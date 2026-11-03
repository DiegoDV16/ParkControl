<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/TarifaController.php
 * ==========================================================================
 * Administración de tarifas de cobro (solo Administrador).
 * ==========================================================================
 */

class TarifaController
{
    public function index(): void
    {
        $tarifas = Tarifa::todas();

        $titulo = 'Tarifas';
        $seccion = 'tarifas';
        require PC_APP . '/views/tarifas/index.php';
    }

    public function crear(): void
    {
        $tarifa = [
            'idTarifa'        => 0,
            'nombre'          => '',
            'valorHora'       => '',
            'fraccionMinutos' => 60,
            'valorFraccion'   => '',
            'activo'          => 1,
        ];
        $modo = 'crear';
        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);

        $titulo = 'Nueva tarifa';
        $seccion = 'tarifas';
        require PC_APP . '/views/tarifas/form.php';
    }

    public function guardar(): void
    {
        $datos = $this->datosFormulario();
        $v = $this->validar($datos, 0);

        if (!$v->valido()) {
            $this->volverConErrores($v->errores(), $datos, 'tarifas/crear');
        }

        $id = Tarifa::crear($datos);
        Auditoria::registrar('crear', 'tarifa', $id, 'Tarifa ' . $datos['nombre']);
        Flash::exito('Tarifa creada correctamente.');
        redirigir('tarifas');
    }

    public function editar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $tarifa = Tarifa::porId($id);
        if ($tarifa === null) {
            Flash::error('La tarifa solicitada no existe.');
            redirigir('tarifas');
        }

        $modo = 'editar';
        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);

        $titulo = 'Editar tarifa';
        $seccion = 'tarifas';
        require PC_APP . '/views/tarifas/form.php';
    }

    public function actualizar(): void
    {
        $id = (int) ($_POST['idTarifa'] ?? 0);
        if (Tarifa::porId($id) === null) {
            Flash::error('La tarifa solicitada no existe.');
            redirigir('tarifas');
        }

        $datos = $this->datosFormulario();
        $v = $this->validar($datos, $id);

        if (!$v->valido()) {
            $this->volverConErrores($v->errores(), $datos, 'tarifas/editar', ['id' => $id]);
        }

        Tarifa::actualizar($id, $datos);
        Auditoria::registrar('actualizar', 'tarifa', $id, 'Tarifa ' . $datos['nombre']);
        Flash::exito('Tarifa actualizada correctamente.');
        redirigir('tarifas');
    }

    public function estado(): void
    {
        $id = (int) ($_POST['idTarifa'] ?? 0);
        $tarifa = Tarifa::porId($id);

        if ($tarifa === null) {
            Flash::error('La tarifa solicitada no existe.');
            redirigir('tarifas');
        }

        $nuevo = (int) $tarifa['activo'] === 1 ? 0 : 1;

        if ($nuevo === 0 && count(Tarifa::activas()) <= 1) {
            Flash::error('Debe quedar al menos una tarifa activa.');
            redirigir('tarifas');
        }

        Tarifa::cambiarEstado($id, $nuevo);
        Auditoria::registrar($nuevo === 1 ? 'activar' : 'desactivar', 'tarifa', $id, $tarifa['nombre']);
        Flash::exito($nuevo === 1 ? 'Tarifa activada.' : 'Tarifa desactivada.');
        redirigir('tarifas');
    }

    /** @return array<string, mixed> */
    private function datosFormulario(): array
    {
        $valorFraccion = trim((string) ($_POST['valorFraccion'] ?? ''));

        return [
            'nombre'          => trim((string) ($_POST['nombre'] ?? '')),
            'valorHora'       => (float) str_replace(['.', ','], ['', '.'], (string) ($_POST['valorHora'] ?? '0')),
            'fraccionMinutos' => (int) ($_POST['fraccionMinutos'] ?? 60),
            'valorFraccion'   => $valorFraccion === ''
                ? null
                : (float) str_replace(['.', ','], ['', '.'], $valorFraccion),
        ];
    }

    private function validar(array $datos, int $idActual): Validador
    {
        $v = (new Validador([
            'nombre'          => $datos['nombre'],
            'valorHora'       => (string) $datos['valorHora'],
            'fraccionMinutos' => (string) $datos['fraccionMinutos'],
        ]))
            ->requerido('nombre', 'nombre')
            ->longitud('nombre', 'nombre', 2, 50)
            ->entero('fraccionMinutos', 'fracción', 5, 1440);

        if ($datos['valorHora'] <= 0) {
            $v->agregar('valorHora', 'El valor por hora debe ser mayor que cero.');
        }

        if ($datos['valorFraccion'] !== null && $datos['valorFraccion'] < 0) {
            $v->agregar('valorFraccion', 'El valor por fracción no puede ser negativo.');
        }

        if ($datos['nombre'] !== '' && Tarifa::existeNombre($datos['nombre'], $idActual)) {
            $v->agregar('nombre', 'Ya existe una tarifa con ese nombre.');
        }

        return $v;
    }

    private function volverConErrores(array $errores, array $datos, string $ruta, array $parametros = []): never
    {
        $_SESSION['_errores'] = $errores;
        $_SESSION['_old'] = $datos;
        Flash::error('Revisa los datos de la tarifa.');
        redirigir($ruta, $parametros);
    }
}
