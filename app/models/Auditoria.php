<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Auditoria.php
 * ==========================================================================
 * Registro de acciones sensibles (altas, bajas, cambios de rol, cobros).
 * ==========================================================================
 */

class Auditoria
{
    public static function registrar(string $accion, ?string $entidad = null, ?int $idEntidad = null, ?string $detalle = null): void
    {
        $usuario = Auth::usuario();

        $stmt = Database::getConexion()->prepare(
            'INSERT INTO auditoria (idUsuario, accion, entidad, idEntidad, detalle, ip)
             VALUES (:idUsuario, :accion, :entidad, :idEntidad, :detalle, :ip)'
        );
        $stmt->execute([
            ':idUsuario' => $usuario['id'] ?? null,
            ':accion'    => $accion,
            ':entidad'   => $entidad,
            ':idEntidad' => $idEntidad,
            ':detalle'   => $detalle,
            ':ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    public static function ultimas(int $limite = 50): array
    {
        $limite = max(1, min(200, $limite));
        $sql = 'SELECT a.*, u.nombres, u.apellidoPaterno
                FROM auditoria a
                LEFT JOIN usuario u ON u.idUsuario = a.idUsuario
                ORDER BY a.idAuditoria DESC
                LIMIT ' . $limite;
        return Database::getConexion()->query($sql)->fetchAll();
    }
}
