<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Ingreso.php
 * ==========================================================================
 * Ingresos del estacionamiento: entrada por fila, salida con cobro por día
 * y anulación. La ocupación se calcula con fechaTermino IS NULL.
 * ==========================================================================
 */

class Ingreso
{
    /** Consulta base con cliente, vehículo, fila, servicio y tipo de vehículo. */
    private static function base(): string
    {
        return 'SELECT i.*,
                       c.nombres, c.apellidoPaterno, c.apellidoMaterno, c.rut AS clienteRut,
                       v.patente,
                       f.fila,
                       se.servicioExtra,
                       tv.tipoVehiculo,
                       u.nombres AS usuarioNombres, u.apellidoPaterno AS usuarioApellido
                FROM ingreso i
                INNER JOIN cliente c ON c.idCliente = i.idCliente
                INNER JOIN vehiculo v ON v.idVehiculo = i.idVehiculo
                INNER JOIN fila f ON f.idFila = i.idFilaEstacionamiento
                INNER JOIN usuario u ON u.idUsuario = i.idUsuario
                LEFT JOIN servicioextra se ON se.idServicioExtra = i.idServicioExtra
                LEFT JOIN tipovehiculo tv ON tv.idTipoVehiculo = v.idTipoVehiculo';
    }

    public static function abiertos(): int
    {
        return (int) Database::getConexion()
            ->query('SELECT COUNT(*) FROM ingreso WHERE fechaTermino IS NULL')
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
        $sql = 'SELECT COALESCE(SUM(p.monto), 0) FROM pago p WHERE DATE(p.fechaPago) = CURDATE()';
        return (float) Database::getConexion()->query($sql)->fetchColumn();
    }

    /** Últimos ingresos registrados (panel). */
    public static function ultimos(int $limite = 6): array
    {
        $limite = max(1, min(50, $limite));
        $sql = 'SELECT i.idIngreso, i.fechaInicio, i.horaInicio, i.fechaTermino,
                       i.montoTotal, i.estado,
                       c.nombres, c.apellidoPaterno, c.apellidoMaterno,
                       v.patente, f.fila
                FROM ingreso i
                INNER JOIN cliente c ON c.idCliente = i.idCliente
                INNER JOIN vehiculo v ON v.idVehiculo = i.idVehiculo
                INNER JOIN fila f ON f.idFila = i.idFilaEstacionamiento
                ORDER BY i.idIngreso DESC
                LIMIT ' . $limite;
        return Database::getConexion()->query($sql)->fetchAll();
    }

    /** Listado filtrable para la pantalla de ingresos. */
    public static function listar(string $estado = '', string $busqueda = '', string $desde = '', string $hasta = ''): array
    {
        $sql = self::base();
        $condiciones = [];
        $parametros = [];

        if ($estado === 'abierto') {
            $condiciones[] = 'i.fechaTermino IS NULL';
        } elseif ($estado === 'cerrado') {
            $condiciones[] = "i.fechaTermino IS NOT NULL AND i.estado = 'cerrado'";
        } elseif ($estado === 'anulado') {
            $condiciones[] = "i.estado = 'anulado'";
        }

        if ($busqueda !== '') {
            $condiciones[] = '(v.patente LIKE :q OR c.nombres LIKE :q OR c.apellidoPaterno LIKE :q OR f.fila LIKE :q)';
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
            'SELECT * FROM ingreso WHERE idVehiculo = :id AND fechaTermino IS NULL LIMIT 1'
        );
        $stmt->execute([':id' => $idVehiculo]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /**
     * Registra la entrada: valida la capacidad de la fila y crea el ingreso
     * dentro de una transacción. Lanza RuntimeException si la fila está llena.
     */
    public static function crear(array $datos): int
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $fila = $pdo->prepare('SELECT capacidadTotal FROM fila WHERE idFila = :id FOR UPDATE');
            $fila->execute([':id' => $datos['idFila']]);
            $capacidad = (int) $fila->fetchColumn();

            if ($capacidad <= 0) {
                throw new RuntimeException('La fila seleccionada no existe.');
            }

            $contar = $pdo->prepare(
                'SELECT COUNT(*) FROM ingreso WHERE idFilaEstacionamiento = :id AND fechaTermino IS NULL'
            );
            $contar->execute([':id' => $datos['idFila']]);
            $ocupadas = (int) $contar->fetchColumn();

            if ($ocupadas >= $capacidad) {
                throw new RuntimeException('La fila seleccionada ya está completa.');
            }

            $stmt = $pdo->prepare(
                "INSERT INTO ingreso
                    (idServicioExtra, idUsuario, idVehiculo, idFilaEstacionamiento, idCliente,
                     fechaInicio, horaInicio, fechaRegreso, horaRegreso,
                     numeroVuelo, llaves, observacion, estado)
                 VALUES
                    (:idServicioExtra, :idUsuario, :idVehiculo, :idFila, :idCliente,
                     CURDATE(), CURTIME(), :fechaRegreso, :horaRegreso,
                     :numeroVuelo, :llaves, :observacion, 'abierto')"
            );
            $stmt->execute([
                ':idServicioExtra' => $datos['idServicioExtra'] > 0 ? $datos['idServicioExtra'] : null,
                ':idUsuario'       => $datos['idUsuario'],
                ':idVehiculo'      => $datos['idVehiculo'],
                ':idFila'          => $datos['idFila'],
                ':idCliente'       => $datos['idCliente'],
                ':fechaRegreso'    => $datos['fechaRegreso'] !== '' ? $datos['fechaRegreso'] : null,
                ':horaRegreso'     => $datos['horaRegreso'] !== '' ? $datos['horaRegreso'] : null,
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

    /** Cierra el ingreso con el monto calculado y registra el pago. */
    public static function cerrar(int $id, float $monto, string $metodo, float $recibido, float $vuelto): void
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "UPDATE ingreso
                 SET fechaTermino = CURDATE(), horaTermino = CURTIME(), montoTotal = :monto
                 WHERE idIngreso = :id AND fechaTermino IS NULL AND estado = 'abierto'"
            );
            $stmt->execute([':monto' => $monto, ':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('El ingreso ya fue cerrado o anulado.');
            }

            $estado = $pdo->prepare("UPDATE ingreso SET estado = 'cerrado' WHERE idIngreso = :id");
            $estado->execute([':id' => $id]);

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

    /** Anula un ingreso activo sin cobrar. */
    public static function anular(int $id): void
    {
        $stmt = Database::getConexion()->prepare(
            "UPDATE ingreso SET fechaTermino = CURDATE(), horaTermino = CURTIME(), estado = 'anulado'
             WHERE idIngreso = :id AND fechaTermino IS NULL AND estado = 'abierto'"
        );
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Solo se pueden anular ingresos activos.');
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

    /** Monto a cobrar: tramo de días + servicio según tipo de vehículo. */
    public static function montoEstimado(array $ingreso, int $minutos): float
    {
        return Tarifa::total(
            $minutos,
            (string) ($ingreso['tipoVehiculo'] ?? 'SEDAN'),
            (string) ($ingreso['servicioExtra'] ?? 'Sin servicio')
        );
    }
}
