<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Fila.php
 * ==========================================================================
 * Filas o sectores del estacionamiento y su capacidad.
 * ==========================================================================
 */

class Fila
{
    /** Filas con el conteo real de plazas registradas. */
    public static function todas(): array
    {
        $sql = 'SELECT f.idFila, f.fila, f.capacidadTotal, f.estado,
                       COUNT(p.idPlaza) AS plazas,
                       COALESCE(SUM(p.estado = 1), 0) AS libres,
                       COALESCE(SUM(p.estado = 2), 0) AS ocupadas
                FROM fila f
                LEFT JOIN plaza p ON p.idFila = f.idFila
                GROUP BY f.idFila, f.fila, f.capacidadTotal, f.estado
                ORDER BY f.fila';
        return Database::getConexion()->query($sql)->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare('SELECT * FROM fila WHERE idFila = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function activas(): array
    {
        return Database::getConexion()
            ->query('SELECT idFila, fila, capacidadTotal FROM fila WHERE estado = 1 ORDER BY fila')
            ->fetchAll();
    }

    public static function existe(string $nombre): bool
    {
        $stmt = Database::getConexion()->prepare('SELECT 1 FROM fila WHERE fila = :fila LIMIT 1');
        $stmt->execute([':fila' => $nombre]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Crea la fila y genera automáticamente sus plazas numeradas.
     * Devuelve el id de la fila creada.
     */
    public static function crear(string $nombre, int $capacidad): int
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('INSERT INTO fila (fila, capacidadTotal, estado) VALUES (?, ?, 1)');
            $stmt->execute([$nombre, $capacidad]);
            $idFila = (int) $pdo->lastInsertId();

            $insertar = $pdo->prepare('INSERT INTO plaza (idFila, numero, estado) VALUES (?, ?, 1)');
            $ancho = max(2, strlen((string) $capacidad));
            for ($numero = 1; $numero <= $capacidad; $numero++) {
                $insertar->execute([$idFila, $nombre . str_pad((string) $numero, $ancho, '0', STR_PAD_LEFT)]);
            }

            $pdo->commit();
            return $idFila;
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }
}
