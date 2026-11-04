<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Fila.php
 * ==========================================================================
 * Filas o sectores del estacionamiento. Cada fila tiene una capacidad fija
 * y no se numeran las plazas: la ocupación se obtiene contando los ingresos
 * activos (fechaTermino IS NULL).
 * ==========================================================================
 */

class Fila
{
    /** Todas las filas con su ocupación actual. */
    public static function todas(): array
    {
        $sql = 'SELECT f.idFila, f.fila, f.capacidadTotal, f.estado,
                       (SELECT COUNT(*) FROM ingreso i
                        WHERE i.idFilaEstacionamiento = f.idFila
                          AND i.fechaTermino IS NULL) AS ocupadas
                FROM fila f
                ORDER BY f.fila';
        return Database::getConexion()->query($sql)->fetchAll();
    }

    /** Filas con espacio disponible. */
    public static function disponibles(): array
    {
        $filas = [];
        foreach (self::todas() as $fila) {
            if ((int) $fila['estado'] === 1
                && (int) $fila['ocupadas'] < (int) $fila['capacidadTotal']) {
                $fila['libres'] = (int) $fila['capacidadTotal'] - (int) $fila['ocupadas'];
                $filas[] = $fila;
            }
        }
        return $filas;
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare('SELECT * FROM fila WHERE idFila = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function existe(string $nombre): bool
    {
        $stmt = Database::getConexion()->prepare('SELECT 1 FROM fila WHERE fila = :fila LIMIT 1');
        $stmt->execute([':fila' => $nombre]);
        return (bool) $stmt->fetchColumn();
    }

    public static function crear(string $nombre, int $capacidad): int
    {
        $stmt = Database::getConexion()->prepare(
            'INSERT INTO fila (fila, capacidadTotal, estado) VALUES (?, ?, 1)'
        );
        $stmt->execute([$nombre, $capacidad]);
        return (int) Database::getConexion()->lastInsertId();
    }

    /**
     * Resumen global de capacidad: total de espacios, ocupados y libres.
     *
     * @return array{filas: int, total: int, ocupadas: int, libres: int, llenas: int}
     */
    public static function resumen(): array
    {
        $total = 0;
        $ocupadas = 0;
        $llenas = 0;
        $filas = self::todas();

        foreach ($filas as $fila) {
            $total += (int) $fila['capacidadTotal'];
            $ocupadas += (int) $fila['ocupadas'];
            if ((int) $fila['ocupadas'] >= (int) $fila['capacidadTotal']) {
                $llenas++;
            }
        }

        return [
            'filas'    => count($filas),
            'total'    => $total,
            'ocupadas' => $ocupadas,
            'libres'   => max(0, $total - $ocupadas),
            'llenas'   => $llenas,
        ];
    }

    /** Ingresos activos dentro de una fila. */
    public static function ocupadas(int $idFila): int
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT COUNT(*) FROM ingreso
             WHERE idFilaEstacionamiento = :id AND fechaTermino IS NULL'
        );
        $stmt->execute([':id' => $idFila]);
        return (int) $stmt->fetchColumn();
    }
}
