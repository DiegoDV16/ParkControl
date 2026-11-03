<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Tarifa.php
 * ==========================================================================
 * Tarifas de cobro por hora o fracción.
 * ==========================================================================
 */

class Tarifa
{
    public static function todas(): array
    {
        return Database::getConexion()
            ->query('SELECT * FROM tarifa ORDER BY activo DESC, nombre')
            ->fetchAll();
    }

    public static function activas(): array
    {
        return Database::getConexion()
            ->query('SELECT * FROM tarifa WHERE activo = 1 ORDER BY nombre')
            ->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare('SELECT * FROM tarifa WHERE idTarifa = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /** Tarifa activa con menor id: se usa como predeterminada al ingresar. */
    public static function predeterminada(): ?array
    {
        $fila = Database::getConexion()
            ->query('SELECT * FROM tarifa WHERE activo = 1 ORDER BY idTarifa LIMIT 1')
            ->fetch();
        return $fila ?: null;
    }

    public static function crear(array $datos): int
    {
        $sql = 'INSERT INTO tarifa (nombre, valorHora, fraccionMinutos, valorFraccion, activo)
                VALUES (:nombre, :valorHora, :fraccionMinutos, :valorFraccion, 1)';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':nombre'          => $datos['nombre'],
            ':valorHora'       => $datos['valorHora'],
            ':fraccionMinutos' => $datos['fraccionMinutos'],
            ':valorFraccion'   => $datos['valorFraccion'],
        ]);
        return (int) Database::getConexion()->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): void
    {
        $sql = 'UPDATE tarifa
                SET nombre = :nombre, valorHora = :valorHora,
                    fraccionMinutos = :fraccionMinutos, valorFraccion = :valorFraccion
                WHERE idTarifa = :id';
        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute([
            ':nombre'          => $datos['nombre'],
            ':valorHora'       => $datos['valorHora'],
            ':fraccionMinutos' => $datos['fraccionMinutos'],
            ':valorFraccion'   => $datos['valorFraccion'],
            ':id'              => $id,
        ]);
    }

    public static function cambiarEstado(int $id, int $estado): void
    {
        $stmt = Database::getConexion()->prepare('UPDATE tarifa SET activo = :estado WHERE idTarifa = :id');
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function existeNombre(string $nombre, int $excepto = 0): bool
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT 1 FROM tarifa WHERE nombre = :nombre AND idTarifa <> :excepto LIMIT 1'
        );
        $stmt->execute([':nombre' => $nombre, ':excepto' => $excepto]);
        return (bool) $stmt->fetchColumn();
    }

    /** Calcula el monto a cobrar por una estadía en minutos. */
    public static function calcular(array $tarifa, int $minutos): float
    {
        if ($minutos <= 0) {
            return 0.0;
        }

        $fraccion = max(1, (int) $tarifa['fraccionMinutos']);
        $valorHora = (float) $tarifa['valorHora'];
        $valorFraccion = $tarifa['valorFraccion'] !== null
            ? (float) $tarifa['valorFraccion']
            : $valorHora / (60 / $fraccion);

        $bloques = (int) ceil($minutos / $fraccion);

        return round($bloques * $valorFraccion, 0);
    }
}
