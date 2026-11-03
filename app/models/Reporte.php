<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Reporte.php
 * ==========================================================================
 * Consultas agregadas de recaudación para la pantalla de reportes.
 * ==========================================================================
 */

class Reporte
{
    /** @return array{ingresos: int, recaudado: float, promedio: float} */
    public static function resumen(string $desde, string $hasta): array
    {
        $sql = 'SELECT COUNT(DISTINCT i.idIngreso) AS ingresos,
                       COALESCE(SUM(p.monto), 0) AS recaudado,
                       COALESCE(AVG(p.monto), 0) AS promedio
                FROM pago p
                INNER JOIN ingreso i ON i.idIngreso = p.idIngreso
                WHERE p.fechaPago >= :desde AND p.fechaPago < DATE_ADD(:hasta, INTERVAL 1 DAY)';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        $fila = $stmt->fetch();

        return [
            'ingresos'  => (int) $fila['ingresos'],
            'recaudado' => (float) $fila['recaudado'],
            'promedio'  => (float) $fila['promedio'],
        ];
    }

    /** Recaudación agrupada por medio de pago. */
    public static function porMetodo(string $desde, string $hasta): array
    {
        $sql = 'SELECT p.metodo, COUNT(*) AS pagos, COALESCE(SUM(p.monto), 0) AS total
                FROM pago p
                WHERE p.fechaPago >= :desde AND p.fechaPago < DATE_ADD(:hasta, INTERVAL 1 DAY)
                GROUP BY p.metodo
                ORDER BY total DESC';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll();
    }

    /** Recaudación por día del período. */
    public static function porDia(string $desde, string $hasta): array
    {
        $sql = 'SELECT DATE(p.fechaPago) AS dia, COUNT(*) AS pagos, COALESCE(SUM(p.monto), 0) AS total
                FROM pago p
                WHERE p.fechaPago >= :desde AND p.fechaPago < DATE_ADD(:hasta, INTERVAL 1 DAY)
                GROUP BY DATE(p.fechaPago)
                ORDER BY dia';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll();
    }

    /** Detalle de pagos del período. */
    public static function pagos(string $desde, string $hasta, int $limite = 100): array
    {
        $limite = max(1, min(1000, $limite));
        $sql = "SELECT p.idPago, p.fechaPago, p.metodo, p.monto, p.vuelto,
                       i.idIngreso, i.fechaInicio, i.horaInicio, i.fechaTermino, i.horaTermino,
                       v.patente,
                       c.nombres, c.apellidoPaterno, c.apellidoMaterno
                FROM pago p
                INNER JOIN ingreso i ON i.idIngreso = p.idIngreso
                INNER JOIN vehiculo v ON v.idVehiculo = i.idVehiculo
                INNER JOIN cliente c ON c.idCliente = i.idCliente
                WHERE p.fechaPago >= :desde AND p.fechaPago < DATE_ADD(:hasta, INTERVAL 1 DAY)
                ORDER BY p.fechaPago DESC
                LIMIT " . $limite;
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll();
    }
}
