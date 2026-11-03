<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/DashboardController.php
 * ==========================================================================
 * Panel de inicio con indicadores reales del estacionamiento.
 * ==========================================================================
 */

class DashboardController
{
    public function index(): void
    {
        $plazas = Plaza::resumen();
        $ingresosAbiertos = Ingreso::abiertos();
        $ingresosHoy = Ingreso::hoy();
        $recaudacionHoy = Ingreso::recaudacionHoy();
        $ultimosIngresos = Ingreso::ultimos(6);
        $totalUsuarios = Auth::esAdmin() ? Usuario::contar() : null;
        $ocupacion = $plazas['total'] > 0
            ? (int) round($plazas['ocupadas'] * 100 / $plazas['total'])
            : 0;

        require PC_APP . '/views/dashboard/index.php';
    }
}
