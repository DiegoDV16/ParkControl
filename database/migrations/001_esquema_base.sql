-- ==========================================================================
-- ParkControl · 001_esquema_base.sql
-- --------------------------------------------------------------------------
-- Cambios ADITIVOS sobre la base existente. No elimina filas ni columnas:
-- agrega estados, auditoría, catálogos y las tablas del negocio
-- (plaza, tarifa, pago, configuración y auditoría).
-- Se aplica una sola vez mediante database/migrate.php.
-- ==========================================================================

SET NAMES utf8mb4;

-- 1) Roles: normaliza los nombres y evita duplicados por mayúsculas.
UPDATE rol SET rol = 'Operador' WHERE idRol = 2;
UPDATE rol SET rol = 'Administrador' WHERE idRol = 1;
INSERT INTO rol (rol)
SELECT 'Operador' FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM rol WHERE rol = 'Operador');
ALTER TABLE rol ADD UNIQUE KEY uq_rol_nombre (rol);

-- 2) Usuarios: estado, último acceso y fechas de auditoría.
ALTER TABLE usuario
  ADD COLUMN estado TINYINT(1) NOT NULL DEFAULT 1 AFTER contrasena,
  ADD COLUMN ultimoAcceso DATETIME NULL DEFAULT NULL AFTER estado,
  ADD COLUMN creadoEn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER ultimoAcceso,
  ADD COLUMN actualizadoEn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER creadoEn;

-- 3) Clientes: estado y fecha de alta.
ALTER TABLE cliente
  ADD COLUMN estado TINYINT(1) NOT NULL DEFAULT 1 AFTER correo,
  ADD COLUMN creadoEn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER estado,
  ADD KEY idx_cliente_correo (correo);

-- 4) Vehículos: dueño del vehículo (evita asociar un auto a otro cliente).
ALTER TABLE vehiculo
  ADD COLUMN idCliente INT(11) NULL DEFAULT NULL AFTER idVehiculo,
  ADD KEY idx_vehiculo_cliente (idCliente),
  ADD CONSTRAINT fk_vehiculo_cliente FOREIGN KEY (idCliente)
      REFERENCES cliente (idCliente) ON DELETE SET NULL ON UPDATE CASCADE;

-- 5) Catálogos: unicidad y valor de los servicios extra.
ALTER TABLE fila ADD UNIQUE KEY uq_fila_nombre (fila);
ALTER TABLE marca ADD UNIQUE KEY uq_marca_nombre (marca);
ALTER TABLE modelo ADD UNIQUE KEY uq_modelo_nombre (idMarca, modelo);
ALTER TABLE servicioextra
  ADD COLUMN valor DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER servicioExtra,
  ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER valor,
  ADD UNIQUE KEY uq_servicio_nombre (servicioExtra);

-- 6) Plazas individuales por fila/sector.
CREATE TABLE IF NOT EXISTS plaza (
  idPlaza INT(11) NOT NULL AUTO_INCREMENT,
  idFila INT(11) NOT NULL,
  numero VARCHAR(10) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = libre, 2 = ocupada, 0 = fuera de servicio',
  PRIMARY KEY (idPlaza),
  UNIQUE KEY uq_plaza_fila_numero (idFila, numero),
  KEY idx_plaza_estado (estado),
  CONSTRAINT fk_plaza_fila FOREIGN KEY (idFila) REFERENCES fila (idFila)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7) Tarifas de cobro.
CREATE TABLE IF NOT EXISTS tarifa (
  idTarifa INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  valorHora DECIMAL(10,2) NOT NULL,
  fraccionMinutos SMALLINT(6) NOT NULL DEFAULT 60,
  valorFraccion DECIMAL(10,2) NULL DEFAULT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creadoEn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idTarifa),
  UNIQUE KEY uq_tarifa_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8) Ingresos: plaza, tarifa, monto y estado del ticket.
ALTER TABLE ingreso
  ADD COLUMN idPlaza INT(11) NULL DEFAULT NULL AFTER idCliente,
  ADD COLUMN idTarifa INT(11) NULL DEFAULT NULL AFTER idPlaza,
  ADD COLUMN montoTotal DECIMAL(10,2) NULL DEFAULT NULL AFTER observacion,
  ADD COLUMN estado ENUM('abierto','cerrado','anulado') NOT NULL DEFAULT 'abierto' AFTER montoTotal,
  ADD COLUMN creadoEn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER estado,
  ADD KEY idx_ingreso_plaza (idPlaza),
  ADD KEY idx_ingreso_estado (estado),
  ADD KEY idx_ingreso_fechas (fechaInicio, fechaTermino),
  ADD CONSTRAINT fk_ingreso_plaza FOREIGN KEY (idPlaza) REFERENCES plaza (idPlaza)
      ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT fk_ingreso_tarifa FOREIGN KEY (idTarifa) REFERENCES tarifa (idTarifa)
      ON DELETE SET NULL ON UPDATE CASCADE;

-- 9) Pagos de cada ingreso.
CREATE TABLE IF NOT EXISTS pago (
  idPago INT(11) NOT NULL AUTO_INCREMENT,
  idIngreso INT(11) NOT NULL,
  idUsuario INT(11) NOT NULL,
  metodo ENUM('efectivo','debito','credito','transferencia') NOT NULL DEFAULT 'efectivo',
  monto DECIMAL(10,2) NOT NULL,
  vuelto DECIMAL(10,2) NULL DEFAULT NULL,
  referencia VARCHAR(50) NULL DEFAULT NULL,
  fechaPago DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idPago),
  KEY idx_pago_ingreso (idIngreso),
  KEY idx_pago_fecha (fechaPago),
  KEY idx_pago_usuario (idUsuario),
  CONSTRAINT fk_pago_ingreso FOREIGN KEY (idIngreso) REFERENCES ingreso (idIngreso)
      ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pago_usuario FOREIGN KEY (idUsuario) REFERENCES usuario (idUsuario)
      ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10) Parámetros editables del sistema.
CREATE TABLE IF NOT EXISTS configuracion (
  clave VARCHAR(50) NOT NULL,
  valor VARCHAR(255) NOT NULL,
  descripcion VARCHAR(150) NULL DEFAULT NULL,
  PRIMARY KEY (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11) Bitácora de acciones sensibles.
CREATE TABLE IF NOT EXISTS auditoria (
  idAuditoria BIGINT(20) NOT NULL AUTO_INCREMENT,
  idUsuario INT(11) NULL DEFAULT NULL,
  accion VARCHAR(50) NOT NULL,
  entidad VARCHAR(50) NULL DEFAULT NULL,
  idEntidad INT(11) NULL DEFAULT NULL,
  detalle VARCHAR(255) NULL DEFAULT NULL,
  ip VARCHAR(45) NULL DEFAULT NULL,
  creadoEn DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (idAuditoria),
  KEY idx_auditoria_fecha (creadoEn),
  KEY idx_auditoria_usuario (idUsuario),
  CONSTRAINT fk_auditoria_usuario FOREIGN KEY (idUsuario) REFERENCES usuario (idUsuario)
      ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compatibilidad: ingresos antiguos con salida registrada quedan cerrados.
UPDATE ingreso SET estado = 'cerrado' WHERE fechaTermino IS NOT NULL AND estado = 'abierto';
