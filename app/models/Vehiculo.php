<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Vehiculo.php
 * ==========================================================================
 * Vehículos con marca, modelo, tipo y propietario.
 * ==========================================================================
 */

class Vehiculo
{
    private const SELECT_BASE = 'SELECT v.*, m.modelo, ma.marca, tv.tipoVehiculo,
                                        c.nombres, c.apellidoPaterno, c.apellidoMaterno
                                 FROM vehiculo v
                                 INNER JOIN modelo m ON m.idModelo = v.idModelo
                                 INNER JOIN marca ma ON ma.idMarca = m.idMarca
                                 LEFT JOIN tipovehiculo tv ON tv.idTipoVehiculo = v.idTipoVehiculo
                                 LEFT JOIN cliente c ON c.idCliente = v.idCliente';

    public static function listar(string $busqueda = ''): array
    {
        $sql = self::SELECT_BASE;
        $parametros = [];

        if ($busqueda !== '') {
            $sql .= ' WHERE (v.patente LIKE :q OR ma.marca LIKE :q OR m.modelo LIKE :q
                      OR tv.tipoVehiculo LIKE :q OR c.nombres LIKE :q OR c.apellidoPaterno LIKE :q)';
            $parametros[':q'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY v.idVehiculo DESC';

        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare(self::SELECT_BASE . ' WHERE v.idVehiculo = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function crear(array $datos): int
    {
        $sql = 'INSERT INTO vehiculo (idCliente, patente, anio, color, idModelo, idTipoVehiculo)
                VALUES (:idCliente, :patente, :anio, :color, :idModelo, :idTipoVehiculo)';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':idCliente'      => $datos['idCliente'] > 0 ? $datos['idCliente'] : null,
            ':patente'        => $datos['patente'],
            ':anio'           => $datos['anio'] > 0 ? $datos['anio'] : null,
            ':color'          => $datos['color'] !== '' ? $datos['color'] : null,
            ':idModelo'       => $datos['idModelo'],
            ':idTipoVehiculo' => $datos['idTipoVehiculo'],
        ]);

        return (int) Database::getConexion()->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): void
    {
        $sql = 'UPDATE vehiculo
                SET idCliente = :idCliente, patente = :patente, anio = :anio,
                    color = :color, idModelo = :idModelo, idTipoVehiculo = :idTipoVehiculo
                WHERE idVehiculo = :id';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':idCliente'      => $datos['idCliente'] > 0 ? $datos['idCliente'] : null,
            ':patente'        => $datos['patente'],
            ':anio'           => $datos['anio'] > 0 ? $datos['anio'] : null,
            ':color'          => $datos['color'] !== '' ? $datos['color'] : null,
            ':idModelo'       => $datos['idModelo'],
            ':idTipoVehiculo' => $datos['idTipoVehiculo'],
            ':id'             => $id,
        ]);
    }

    public static function eliminar(int $id): void
    {
        $stmt = Database::getConexion()->prepare('DELETE FROM vehiculo WHERE idVehiculo = :id');
        $stmt->execute([':id' => $id]);
    }

    public static function existePatente(string $patente, int $excepto = 0): bool
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT 1 FROM vehiculo WHERE patente = :patente AND idVehiculo <> :excepto LIMIT 1'
        );
        $stmt->execute([':patente' => $patente, ':excepto' => $excepto]);
        return (bool) $stmt->fetchColumn();
    }

    public static function tieneIngresos(int $id): bool
    {
        $stmt = Database::getConexion()->prepare('SELECT 1 FROM ingreso WHERE idVehiculo = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    public static function contar(): int
    {
        return (int) Database::getConexion()->query('SELECT COUNT(*) FROM vehiculo')->fetchColumn();
    }
}
