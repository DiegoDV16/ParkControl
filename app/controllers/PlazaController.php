<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/PlazaController.php
 * ==========================================================================
 * Mapa de plazas por fila y administración de filas/sectores.
 * ==========================================================================
 */

class PlazaController
{
    public function index(): void
    {
        $filas = Fila::todas();
        $plazas = Plaza::porFila();
        $resumen = Plaza::resumen();

        $titulo = 'Plazas';
        $seccion = 'plazas';
        require PC_APP . '/views/plazas/index.php';
    }

    /** Cambia una plaza entre libre y fuera de servicio (nunca a ocupada). */
    public function estado(): void
    {
        $id = (int) ($_POST['idPlaza'] ?? 0);
        $plaza = Plaza::porId($id);

        if ($plaza === null) {
            Flash::error('La plaza solicitada no existe.');
            redirigir('plazas');
        }

        if ((int) $plaza['estado'] === Plaza::OCUPADA) {
            Flash::error('La plaza está ocupada por un ingreso activo; primero registra la salida.');
            redirigir('plazas');
        }

        $nuevoEstado = (int) $plaza['estado'] === Plaza::LIBRE ? Plaza::FUERA_DE_SERVICIO : Plaza::LIBRE;
        Plaza::cambiarEstado($id, $nuevoEstado);
        Auditoria::registrar(
            'estado',
            'plaza',
            $id,
            'Plaza ' . $plaza['numero'] . ' → ' . ($nuevoEstado === Plaza::LIBRE ? 'libre' : 'fuera de servicio')
        );

        Flash::exito('Plaza ' . $plaza['numero'] . ' marcada como '
            . ($nuevoEstado === Plaza::LIBRE ? 'libre.' : 'fuera de servicio.'));
        redirigir('plazas');
    }

    public function crearFila(): void
    {
        $modo = 'crear';
        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);

        $titulo = 'Nueva fila';
        $seccion = 'plazas';
        require PC_APP . '/views/plazas/fila_form.php';
    }

    public function guardarFila(): void
    {
        $nombre = strtoupper(trim((string) ($_POST['fila'] ?? '')));
        $capacidad = (int) ($_POST['capacidadTotal'] ?? 0);

        $v = (new Validador(['fila' => $nombre, 'capacidadTotal' => (string) $capacidad]))
            ->requerido('fila', 'nombre de la fila')
            ->longitud('fila', 'nombre de la fila', 1, 12)
            ->entero('capacidadTotal', 'capacidad', 1, 200);

        if ($v->valido() && Fila::existe($nombre)) {
            $v->agregar('fila', 'Ya existe una fila con ese nombre.');
        }

        if (!$v->valido()) {
            $_SESSION['_errores'] = $v->errores();
            $_SESSION['_old'] = ['fila' => $nombre, 'capacidadTotal' => $capacidad];
            Flash::error('Revisa los datos de la fila.');
            redirigir('plazas/filas/crear');
        }

        $id = Fila::crear($nombre, $capacidad);
        Auditoria::registrar('crear', 'fila', $id, 'Fila ' . $nombre . ' con ' . $capacidad . ' plazas');
        Flash::exito('Fila ' . $nombre . ' creada con ' . $capacidad . ' plazas.');
        redirigir('plazas');
    }

    public function crear(): void
    {
        $filas = Fila::activas();
        $modo = 'crear';
        $errores = $_SESSION['_errores'] ?? [];
        $old = $_SESSION['_old'] ?? [];
        unset($_SESSION['_errores'], $_SESSION['_old']);

        $titulo = 'Nueva plaza';
        $seccion = 'plazas';
        require PC_APP . '/views/plazas/plaza_form.php';
    }

    public function guardar(): void
    {
        $idFila = (int) ($_POST['idFila'] ?? 0);
        $numero = strtoupper(trim((string) ($_POST['numero'] ?? '')));

        $v = (new Validador(['idFila' => (string) $idFila, 'numero' => $numero]))
            ->entero('idFila', 'fila', 1, PHP_INT_MAX)
            ->requerido('numero', 'número de plaza')
            ->longitud('numero', 'número de plaza', 1, 10);

        if ($v->valido() && Fila::porId($idFila) === null) {
            $v->agregar('idFila', 'La fila seleccionada no es válida.');
        }

        if ($v->valido() && Plaza::existeNumero($idFila, $numero)) {
            $v->agregar('numero', 'Ese número ya existe en la fila.');
        }

        if (!$v->valido()) {
            $_SESSION['_errores'] = $v->errores();
            $_SESSION['_old'] = ['idFila' => $idFila, 'numero' => $numero];
            Flash::error('Revisa los datos de la plaza.');
            redirigir('plazas/crear');
        }

        $id = Plaza::crear($idFila, $numero);
        Auditoria::registrar('crear', 'plaza', $id, 'Plaza ' . $numero);
        Flash::exito('Plaza ' . $numero . ' agregada.');
        redirigir('plazas');
    }
}
