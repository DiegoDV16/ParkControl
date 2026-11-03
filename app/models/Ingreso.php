<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Ingreso.php
 * ==========================================================================
 * Consultas de la tabla `ingreso` usadas por el panel y los reportes.
 * ==========================================================================
 */

class Ingreso
{
    public static function abiertos(): int
    {
        return (int) Database::getConexion()
            ->query("SELECT COUNT(*) FROM ingreso WHERE estado = 'abierto'")
            ->fetchColumn();
    }

    public static function hoy(): int
    {
        return (int) Database::getConexion()
            ->query('SELECT COUNT(*) FROM ingreso WHERE fechaInicio = CURDATE()')
            ->fetchColumn();
    }

    public static function recaudacionHoy(): float
    {
        $sql = 'SELECT COALESCE(SUM(p.monto), 0)
                FROM pago p
                WHERE DATE(p.fechaPago) = CURDATE()';
        return (float) Database::getConexion()->query($sql)->fetchColumn();
    }

    /** Últimos ingresos registrados con cliente, vehículo y plaza. */
    public static function ultimos(int $limite = 6): array
    {
        $limite = max(1, min(50, $limite));
        $sql = 'SELECT i.idIngreso, i.fechaInicio, i.horaInicio, i.fechaTermino,
                       i.estado, i.montoTotal,
                       c.nombres, c.apellidoPaterno, c.apellidoMaterno,
                       v.patente,
                       pl.numero AS plaza
                FROM ingreso i
                INNER JOIN cliente c ON c.idCliente = i.idCliente
                INNER JOIN vehiculo v ON v.idVehiculo = i.idVehiculo
                LEFT JOIN plaza pl ON pl.idPlaza = i.idPlaza
                ORDER BY i.idIngreso DESC
                LIMIT ' . $limite;
        return Database::getConexion()->query($sql)->fetchAll();
    }
}
