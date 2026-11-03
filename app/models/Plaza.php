<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Plaza.php
 * ==========================================================================
 * Plazas individuales de estacionamiento y su estado.
 * Estados: 0 = fuera de servicio, 1 = libre, 2 = ocupada.
 * ==========================================================================
 */

class Plaza
{
    public const FUERA_DE_SERVICIO = 0;
    public const LIBRE = 1;
    public const OCUPADA = 2;

    /** @return array{total: int, libres: int, ocupadas: int, fuera: int} */
    public static function resumen(): array
    {
        $fila = Database::getConexion()->query(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(estado = 1), 0) AS libres,
                    COALESCE(SUM(estado = 2), 0) AS ocupadas,
                    COALESCE(SUM(estado = 0), 0) AS fuera
             FROM plaza'
        )->fetch();

        return [
            'total'    => (int) $fila['total'],
            'libres'   => (int) $fila['libres'],
            'ocupadas' => (int) $fila['ocupadas'],
            'fuera'    => (int) $fila['fuera'],
        ];
    }

    /** Plazas agrupadas por fila: [ ['fila' => [...], 'plazas' => [...]], ... ]. */
    public static function porFila(): array
    {
        $sql = 'SELECT p.idPlaza, p.numero, p.estado, f.idFila, f.fila
                FROM plaza p
                INNER JOIN fila f ON f.idFila = p.idFila
                ORDER BY f.fila, CAST(p.numero AS UNSIGNED), p.numero';
        $filas = [];

        foreach (Database::getConexion()->query($sql)->fetchAll() as $plaza) {
            $idFila = (int) $plaza['idFila'];
            if (!isset($filas[$idFila])) {
                $filas[$idFila] = [
                    'fila'   => ['idFila' => $idFila, 'fila' => $plaza['fila']],
                    'plazas' => [],
                ];
            }
            $filas[$idFila]['plazas'][] = $plaza;
        }

        return array_values($filas);
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT p.*, f.fila FROM plaza p INNER JOIN fila f ON f.idFila = p.idFila WHERE p.idPlaza = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function crear(int $idFila, string $numero): int
    {
        $stmt = Database::getConexion()->prepare(
            'INSERT INTO plaza (idFila, numero, estado) VALUES (:idFila, :numero, 1)'
        );
        $stmt->execute([':idFila' => $idFila, ':numero' => $numero]);
        return (int) Database::getConexion()->lastInsertId();
    }

    public static function cambiarEstado(int $id, int $estado): void
    {
        $stmt = Database::getConexion()->prepare('UPDATE plaza SET estado = :estado WHERE idPlaza = :id');
        $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public static function existeNumero(int $idFila, string $numero): bool
    {
        $stmt = Database::getConexion()->prepare(
            'SELECT 1 FROM plaza WHERE idFila = :idFila AND numero = :numero LIMIT 1'
        );
        $stmt->execute([':idFila' => $idFila, ':numero' => $numero]);
        return (bool) $stmt->fetchColumn();
    }

    /** Plazas libres para asignar en un ingreso. */
    public static function libres(): array
    {
        $sql = 'SELECT p.idPlaza, p.numero, f.fila
                FROM plaza p
                INNER JOIN fila f ON f.idFila = p.idFila
                WHERE p.estado = 1
                ORDER BY f.fila, CAST(p.numero AS UNSIGNED)';
        return Database::getConexion()->query($sql)->fetchAll();
    }
}
