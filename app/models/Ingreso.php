<?php
/**
 * ==========================================================================
 * ParkControl · app/models/Ingreso.php
 * ==========================================================================
 * Ingresos del estacionamiento. El registro puede crear (o reutilizar) el
 * cliente y el vehículo a partir de los datos del formulario, y luego inserta
 * la fila en `ingreso` dentro de una sola transacción.
 * ==========================================================================
 */

class Ingreso
{
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
     * Registra un ingreso creando o reutilizando cliente y vehículo.
     * Todo ocurre en una sola transacción.
     */
    public static function registrarConDatos(array $datos): int
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $rut = Rut::formatear($datos['rut']);
            $cliente = Cliente::porRut($rut);

            if ($cliente !== null) {
                $idCliente = (int) $cliente['idCliente'];
                Cliente::actualizar($idCliente, [
                    'rut'             => $rut,
                    'nombres'         => $datos['nombres'],
                    'apellidoPaterno' => $datos['apellidoPaterno'],
                    'apellidoMaterno' => $datos['apellidoMaterno'],
                    'telefono'        => $datos['telefono'],
                    'correo'          => $datos['correo'],
                ]);
            } else {
                $idCliente = Cliente::crear([
                    'rut'             => $rut,
                    'nombres'         => $datos['nombres'],
                    'apellidoPaterno' => $datos['apellidoPaterno'],
                    'apellidoMaterno' => $datos['apellidoMaterno'],
                    'telefono'        => $datos['telefono'],
                    'correo'          => $datos['correo'],
                ]);
            }

            $idModelo = (int) $datos['idModelo'];

            $vehiculo = Vehiculo::porPatente($datos['patente']);

            if ($vehiculo !== null) {
                $duenio = (int) ($vehiculo['idCliente'] ?? 0);
                if ($duenio > 0 && $duenio !== $idCliente) {
                    throw new RuntimeException('La patente ya está registrada para otro cliente.');
                }
                if (self::activoPorVehiculo((int) $vehiculo['idVehiculo']) !== null) {
                    throw new RuntimeException('El vehículo ya tiene un ingreso abierto.');
                }

                $idVehiculo = (int) $vehiculo['idVehiculo'];
                $datos['anio'] = $vehiculo['anio'];
                $datos['color'] = $vehiculo['color'];
                Vehiculo::actualizar($idVehiculo, [
                    'idCliente'      => $idCliente,
                    'patente'        => $datos['patente'],
                    'anio'           => $datos['anio'],
                    'color'          => $datos['color'],
                    'idModelo'       => $idModelo,
                    'idTipoVehiculo' => $datos['idTipoVehiculo'],
                ]);
            } else {
                $datos['anio'] = null;
                $datos['color'] = null;
                $idVehiculo = Vehiculo::crear([
                    'idCliente'      => $idCliente,
                    'patente'        => $datos['patente'],
                    'anio'           => $datos['anio'],
                    'color'          => $datos['color'],
                    'idModelo'       => $idModelo,
                    'idTipoVehiculo' => $datos['idTipoVehiculo'],
                ]);
            }

            self::verificarCapacidad($datos['idFila']);

            $id = self::insertar([
                'idServicioExtra' => $datos['idServicioExtra'],
                'idUsuario'       => $datos['idUsuario'],
                'idVehiculo'      => $idVehiculo,
                'idFila'          => $datos['idFila'],
                'idCliente'       => $idCliente,
                'fechaInicio'     => $datos['fechaInicio'],
                'horaInicio'      => $datos['horaInicio'],
                'fechaRegreso'    => $datos['fechaRegreso'],
                'horaRegreso'     => $datos['horaRegreso'],
                'numeroVuelo'     => $datos['numeroVuelo'],
                'llaves'          => $datos['llaves'],
                'observacion'     => $datos['observacion'],
            ]);

            $pdo->commit();
            return $id;
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }

    /** Comprueba que la fila tenga capacidad y exista. */
    private static function verificarCapacidad(int $idFila): void
    {
        $pdo = Database::getConexion();

        $fila = $pdo->prepare('SELECT capacidadTotal FROM fila WHERE idFila = :id FOR UPDATE');
        $fila->execute([':id' => $idFila]);
        $capacidad = (int) $fila->fetchColumn();

        if ($capacidad <= 0) {
            throw new RuntimeException('La fila seleccionada no existe.');
        }

        $contar = $pdo->prepare(
            'SELECT COUNT(*) FROM ingreso WHERE idFilaEstacionamiento = :id AND fechaTermino IS NULL'
        );
        $contar->execute([':id' => $idFila]);

        if ((int) $contar->fetchColumn() >= $capacidad) {
            throw new RuntimeException('La fila seleccionada ya está completa.');
        }
    }

    /** Inserta el ingreso (sin abrir transacción). */
    private static function insertar(array $datos): int
    {
        $stmt = Database::getConexion()->prepare(
            "INSERT INTO ingreso
                (idServicioExtra, idUsuario, idVehiculo, idFilaEstacionamiento, idCliente,
                 fechaInicio, horaInicio, fechaRegreso, horaRegreso,
                 numeroVuelo, llaves, observacion, estado)
             VALUES
                (:idServicioExtra, :idUsuario, :idVehiculo, :idFila, :idCliente,
                 :fechaInicio, :horaInicio, :fechaRegreso, :horaRegreso,
                 :numeroVuelo, :llaves, :observacion, 'abierto')"
        );
        $stmt->execute([
            ':idServicioExtra' => $datos['idServicioExtra'] > 0 ? $datos['idServicioExtra'] : null,
            ':idUsuario'       => $datos['idUsuario'],
            ':idVehiculo'      => $datos['idVehiculo'],
            ':idFila'          => $datos['idFila'],
            ':idCliente'       => $datos['idCliente'],
            ':fechaInicio'     => $datos['fechaInicio'],
            ':horaInicio'      => $datos['horaInicio'],
            ':fechaRegreso'    => $datos['fechaRegreso'] !== '' ? $datos['fechaRegreso'] : null,
            ':horaRegreso'     => $datos['horaRegreso'] !== '' ? $datos['horaRegreso'] : null,
            ':numeroVuelo'     => $datos['numeroVuelo'] !== '' ? $datos['numeroVuelo'] : null,
            ':llaves'          => $datos['llaves'],
            ':observacion'     => $datos['observacion'] !== '' ? $datos['observacion'] : null,
        ]);

        return (int) Database::getConexion()->lastInsertId();
    }

    /** Cierra el ingreso con el monto calculado y registra el pago. */
    public static function cerrar(int $id, float $monto, string $metodo, float $recibido, float $vuelto): void
    {
        $pdo = Database::getConexion();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "UPDATE ingreso
                 SET fechaTermino = CURDATE(), horaTermino = NOW(), montoTotal = :monto
                 WHERE idIngreso = :id AND fechaTermino IS NULL AND estado = 'abierto'"
            );
            $stmt->execute([':monto' => $monto, ':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new RuntimeException('El ingreso ya fue cerrado o anulado.');
            }

            $pdo->prepare("UPDATE ingreso SET estado = 'cerrado' WHERE idIngreso = :id")
                ->execute([':id' => $id]);

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
            "UPDATE ingreso SET fechaTermino = CURDATE(), horaTermino = NOW(), estado = 'anulado'
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
