<?php
/**
 * ==========================================================================
 * ParkControl · app/controllers/ReporteController.php
 * ==========================================================================
 * Reportes de recaudación por período y exportación a CSV.
 * ==========================================================================
 */

class ReporteController
{
    public function index(): void
    {
        [$desde, $hasta] = $this->rango();

        $resumen = Reporte::resumen($desde, $hasta);
        $porMetodo = Reporte::porMetodo($desde, $hasta);
        $porDia = Reporte::porDia($desde, $hasta);
        $pagos = Reporte::pagos($desde, $hasta);

        $titulo = 'Reportes';
        $seccion = 'reportes';
        require PC_APP . '/views/reportes/index.php';
    }

    public function exportar(): void
    {
        [$desde, $hasta] = $this->rango();
        $pagos = Reporte::pagos($desde, $hasta, 1000);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="parkcontrol-recaudacion-' . $desde . '_' . $hasta . '.csv"');

        $salida = fopen('php://output', 'wb');
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($salida, ['Pago', 'Fecha', 'Patente', 'Cliente', 'Ingreso', 'Entrada', 'Salida', 'Método', 'Monto', 'Vuelto'], ';');

        foreach ($pagos as $p) {
            fputcsv($salida, [
                $p['idPago'],
                $p['fechaPago'],
                $p['patente'],
                nombreCompleto($p),
                $p['idIngreso'],
                $p['fechaInicio'] . ' ' . $p['horaInicio'],
                $p['fechaTermino'] . ' ' . $p['horaTermino'],
                $p['metodo'],
                $p['monto'],
                $p['vuelto'],
            ], ';');
        }

        fclose($salida);
        exit;
    }

    /** @return array{0: string, 1: string} */
    private function rango(): array
    {
        $desde = (string) ($_GET['desde'] ?? '');
        $hasta = (string) ($_GET['hasta'] ?? '');

        $valida = static function (string $fecha): bool {
            $d = DateTime::createFromFormat('Y-m-d', $fecha);
            return $d !== false && $d->format('Y-m-d') === $fecha;
        };

        if (!$valida($desde)) {
            $desde = date('Y-m-01');
        }
        if (!$valida($hasta)) {
            $hasta = date('Y-m-d');
        }
        if ($hasta < $desde) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        return [$desde, $hasta];
    }
}
