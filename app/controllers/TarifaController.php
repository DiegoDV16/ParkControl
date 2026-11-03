<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/TarifaController.php
 * ==========================================================================
 * Tabla de precios vigente (estadía por día y servicios por tipo de
 * vehículo), según las reglas de negocio del proyecto.
 * ==========================================================================
 */

class TarifaController
{
    public function index(): void
    {
        $titulo = 'Tarifas';
        $seccion = 'tarifas';
        require PC_APP . '/views/tarifas/index.php';
    }
}
