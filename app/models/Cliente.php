<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Cliente.php
 * ==========================================================================
 * Acceso a datos de la tabla `cliente`.
 * ==========================================================================
 */

class Cliente
{
    public static function listar(string $busqueda = ''): array
    {
        $sql = 'SELECT * FROM cliente';
        $parametros = [];

        if ($busqueda !== '') {
            $sql .= ' WHERE (nombres LIKE :q OR apellidoPaterno LIKE :q
                      OR apellidoMaterno LIKE :q OR rut LIKE :q OR correo LIKE :q
                      OR telefono LIKE :q)';
            $parametros[':q'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY estado DESC, idCliente DESC';

        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare('SELECT * FROM cliente WHERE idCliente = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function crear(array $datos): int
    {
        $sql = 'INSERT INTO cliente (rut, nombres, apellidoPaterno, apellidoMaterno, telefono, correo, estado)
                VALUES (:rut, :nombres, :apellidoPaterno, :apellidoMaterno, :telefono, :correo, 1)';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':rut'             => $datos['rut'],
            ':nombres'         => $datos['nombres'],
            ':apellidoPaterno' => $datos['apellidoPaterno'],
            ':apellidoMaterno' => $datos['apellidoMaterno'],
            ':telefono'        => $datos['telefono'] !== '' ? $datos['telefono'] : null,
            ':correo'          => $datos['correo'] !== '' ? $datos['correo'] : null,
        ]);

        return (int) Database::getConexion()->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): void
    {
        $sql = 'UPDATE cliente
                SET rut = :rut, nombres = :nombres, apellidoPaterno = :apellidoPaterno,
                    apellidoMaterno = :apellidoMaterno, telefono = :telefono, correo = :correo
                WHERE idCliente = :id';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':rut'             => $datos['rut'],
            ':nombres'         => $datos['nombres'],
            ':apellidoPaterno' => $datos['apellidoPaterno'],
            ':apellidoMaterno' => $datos['apellidoMaterno'],
            ':telefono'        => $datos['telefono'] !== '' ? $datos['telefono'] : null,
            ':correo'          => $datos['correo'] !== '' ? $datos['correo'] : null,
            ':id'              => $id,
        ]);
    }

    public static function cambiarEstado(int $id, int $estado): void
    {
        $stmt = Database::getConexion()->prepare('UPDATE cliente SET estado = :estado WHERE idCliente = :id');
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function existeRut(string $rut, int $excepto = 0): bool
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT 1 FROM cliente WHERE rut = :rut AND idCliente <> :excepto LIMIT 1'
        );
        $stmt->execute([':rut' => $rut, ':excepto' => $excepto]);
        return (bool) $stmt->fetchColumn();
    }

    public static function contar(): int
    {
        return (int) Database::getConexion()->query('SELECT COUNT(*) FROM cliente')->fetchColumn();
    }

    /** Todos los clientes activos (para selects de formularios). */
    public static function activos(): array
    {
        return Database::getConexion()
            ->query('SELECT idCliente, rut, nombres, apellidoPaterno, apellidoMaterno
                     FROM cliente WHERE estado = 1 ORDER BY apellidoPaterno, nombres')
            ->fetchAll();
    }
}
