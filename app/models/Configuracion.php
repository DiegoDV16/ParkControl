<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Configuracion.php
 * ==========================================================================
 * Parámetros editables del sistema (tabla `configuracion`).
 * ==========================================================================
 */

class Configuracion
{
    /** @return array<string, string> */
    public static function todas(): array
    {
        $valores = [];
        foreach (Database::getConexion()->query('SELECT clave, valor FROM configuracion')->fetchAll() as $fila) {
            $valores[$fila['clave']] = $fila['valor'];
        }
        return $valores;
    }

    public static function obtener(string $clave, string $defecto = ''): string
    {
        $stmt = Database::getConexion()->prepare('SELECT valor FROM configuracion WHERE clave = :clave LIMIT 1');
        $stmt->execute([':clave' => $clave]);
        $valor = $stmt->fetchColumn();
        return $valor === false ? $defecto : (string) $valor;
    }

    /** Inserta o actualiza varios parámetros. */
    public static function guardar(array $valores): void
    {
        $stmt = Database::getConexion()->prepare(
            'INSERT INTO configuracion (clave, valor) VALUES (:clave, :valor)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
        );

        foreach ($valores as $clave => $valor) {
            $stmt->execute([':clave' => $clave, ':valor' => $valor]);
        }
    }
}
