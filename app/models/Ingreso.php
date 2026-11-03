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

    /** Consulta base con todos los datos relacionados del ingreso. */
    private static function base(): string
    {
        return 'SELECT i.*,
                       c.nombres, c.apellidoPaterno, c.apellidoMaterno, c.rut AS clienteRut,
                       v.patente,
                       pl.numero AS plaza,
                       f.fila,
                       t.nombre AS tarifaNombre, t.valorHora, t.fraccionMinutos, t.valorFraccion,
                       se.servicioExtra, se.valor AS servicioValor,
                       u.nombres AS usuarioNombres, u.apellidoPaterno AS usuarioApellido
                FROM ingreso i
                INNER JOIN cliente c ON c.idCliente = i.idCliente
                INNER JOIN vehiculo v ON v.idVehiculo = i.idVehiculo
                INNER JOIN usuario u ON u.idUsuario = i.idUsuario
                LEFT JOIN plaza pl ON pl.idPlaza = i.idPlaza
                LEFT JOIN fila f ON f.idFila = pl.idFila
                LEFT JOIN tarifa t ON t.idTarifa = i.idTarifa
                LEFT JOIN servicioextra se ON se.idServicioExtra = i.idServicioExtra';
    }

    /** Listado filtrable para la pantalla de ingresos. */
    public static function listar(string $estado = '', string $busqueda = '', string $desde = '', string $hasta = ''): array
    {
        $sql = self::base();
        $condiciones = [];
        $parametros = [];

        if ($estado !== '') {
            $condiciones[] = 'i.estado = :estado';
            $parametros[':estado'] = $estado;
        }

        if ($busqueda !== '') {
            $condiciones[] = '(v.patente LIKE :q OR c.nombres LIKE :q OR c.apellidoPaterno LIKE :q OR pl.numero LIKE :q)';
            $parametros[':q'] = '%' . $busqueda . '%';
        }

        if ($desde !== '') {
            $condiciones[] = 'i.fechaInicio >= :desde';
            $parametros[':desde'] = $desde;
        }

        if ($hasta !== '') {
            $condiciones[] = 'i.fechaInicio <= :hasta';
            $parametros[':hasta'] = $hasta;
        }

        if ($condiciones !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }

        $sql .= ' ORDER BY i.idIngreso DESC LIMIT 200';

        $stmt = Database::getConexion()->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    public static function porId(int $id): ?array
    {
        $stmt = Database::getConexion()->prepare(self::base() . ' WHERE i.idIngreso = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function activoPorVehiculo(int $idVehiculo): ?array
    {
        $stmt = Database::getConexion()->prepare(
            "SELECT * FROM ingreso WHERE idVehiculo = :id AND estado = 'abierto' LIMIT 1"
        );
        $stmt->execute([':id' => $idVehiculo]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /**
     * Registra la entrada: crea el ingreso y ocupa la plaza en una transacción.
     * Lanza RuntimeException si la plaza ya no está libre.
     */
    public static function crear(array $datos): int
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $ocupar = $pdo->prepare('UPDATE plaza SET estado = 2 WHERE idPlaza = :id AND estado = 1');
            $ocupar->execute([':id' => $datos['idPlaza']]);

            if ($ocupar->rowCount() === 0) {
                throw new RuntimeException('La plaza seleccionada ya no está disponible.');
            }

            $stmt = $pdo->prepare(
                "INSERT INTO ingreso
                    (idServicioExtra, idUsuario, idVehiculo, idFilaEstacionamiento, idCliente,
                     idPlaza, idTarifa, fechaInicio, horaInicio, numeroVuelo, llaves, observacion, estado)
                 VALUES
                    (:idServicioExtra, :idUsuario, :idVehiculo, :idFila, :idCliente,
                     :idPlaza, :idTarifa, CURDATE(), CURTIME(), :numeroVuelo, :llaves, :observacion, 'abierto')"
            );
            $stmt->execute([
                ':idServicioExtra' => $datos['idServicioExtra'] > 0 ? $datos['idServicioExtra'] : null,
                ':idUsuario'       => $datos['idUsuario'],
                ':idVehiculo'      => $datos['idVehiculo'],
                ':idFila'          => $datos['idFila'],
                ':idCliente'       => $datos['idCliente'],
                ':idPlaza'         => $datos['idPlaza'],
                ':idTarifa'        => $datos['idTarifa'],
                ':numeroVuelo'     => $datos['numeroVuelo'] !== '' ? $datos['numeroVuelo'] : null,
                ':llaves'          => $datos['llaves'],
                ':observacion'     => $datos['observacion'] !== '' ? $datos['observacion'] : null,
            ]);

            $id = (int) $pdo->lastInsertId();
            $pdo->commit();
            return $id;
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }

    /** Cierra el ingreso, libera la plaza y registra el pago. */
    public static function cerrar(int $id, float $monto, string $metodo, float $recibido, float $vuelto): void
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "UPDATE ingreso
                 SET fechaTermino = CURDATE(), horaTermino = CURTIME(),
                     montoTotal = :monto, estado = 'cerrado'
                 WHERE idIngreso = :id AND estado = 'abierto'"
            );
            $stmt->execute([':monto' => $monto, ':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('El ingreso ya fue cerrado o anulado.');
            }

            $plaza = $pdo->prepare(
                'UPDATE plaza p INNER JOIN ingreso i ON i.idPlaza = p.idPlaza
                 SET p.estado = 1 WHERE i.idIngreso = :id AND p.estado = 2'
            );
            $plaza->execute([':id' => $id]);

            $pago = $pdo->prepare(
                'INSERT INTO pago (idIngreso, idUsuario, metodo, monto, vuelto)
                 VALUES (:idIngreso, :idUsuario, :metodo, :monto, :vuelto)'
            );
            $pago->execute([
                ':idIngreso' => $id,
                ':idUsuario' => (int) (Auth::usuario()['id'] ?? 0),
                ':metodo'    => $metodo,
                ':monto'     => $monto,
                ':vuelto'    => $vuelto > 0 ? $vuelto : null,
            ]);

            $pdo->commit();
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }

    /** Anula un ingreso abierto y libera su plaza. */
    public static function anular(int $id): void
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("UPDATE ingreso SET estado = 'anulado' WHERE idIngreso = :id AND estado = 'abierto'");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('Solo se pueden anular ingresos abiertos.');
            }

            $pdo->prepare('UPDATE plaza p INNER JOIN ingreso i ON i.idPlaza = p.idPlaza SET p.estado = 1
                           WHERE i.idIngreso = :id AND p.estado = 2')->execute([':id' => $id]);

            $pdo->commit();
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }

    /** Minutos transcurridos desde la entrada (mínimo 1). */
    public static function minutosTranscurridos(array $ingreso, ?DateTimeImmutable $hasta = null): int
    {
        $inicio = new DateTimeImmutable($ingreso['fechaInicio'] . ' ' . $ingreso['horaInicio']);
        $fin = $hasta ?? new DateTimeImmutable();

        if ($fin <= $inicio) {
            return 1;
        }

        return max(1, (int) ceil(($fin->getTimestamp() - $inicio->getTimestamp()) / 60));
    }

    /** Monto estimado: tarifa por tiempo + servicio extra. */
    public static function montoEstimado(array $ingreso, int $minutos): float
    {
        $tarifa = [
            'valorHora'       => $ingreso['valorHora'] ?? 0,
            'fraccionMinutos' => $ingreso['fraccionMinutos'] ?? 60,
            'valorFraccion'   => $ingreso['valorFraccion'] ?? null,
        ];

        $monto = Tarifa::calcular($tarifa, $minutos);
        $monto += (float) ($ingreso['servicioValor'] ?? 0);

        return round($monto, 0);
    }
}
