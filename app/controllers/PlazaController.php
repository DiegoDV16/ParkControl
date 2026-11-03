<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/PlazaController.php
 * ==========================================================================
 * Estado de ocupación por fila (sin plazas numeradas).
 * ==========================================================================
 */

class PlazaController
{
    public function index(): void
    {
        $filas = Fila::todas();
        $resumen = Fila::resumen();

        $titulo = 'Plazas';
        $seccion = 'plazas';
        require PC_APP . '/views/plazas/index.php';
    }
}
