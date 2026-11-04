-- ==========================================================================
-- ParkControl · 002_modelo_filas_tarifas.sql
-- --------------------------------------------------------------------------
-- Alinea el modelo local con el repositorio Bd_Eva01_Base_De_Datos:
--   · Filas reales: A, E, F, G, L y Segundo Patio, 16 espacios cada una.
--   · Sin plazas numeradas: el ingreso se asocia solo a la fila.
--   · Tipos de vehículo (SEDAN, HATCHBACK, CAMIONETA, SUV).
--   · Servicios extra reales (Lavado, Lavado y aspirado, Aspirado, Sin servicio).
--   · Fecha y hora de regreso programadas en el ingreso.
--
-- No elimina ingresos ni usuarios: conserva los registros existentes.
-- ==========================================================================

SET NAMES utf8mb4;

-- 1) Filas: se renombran las existentes para no romper las claves foráneas.
ALTER TABLE fila MODIFY COLUMN fila VARCHAR(15) NOT NULL;

UPDATE fila SET fila = 'A' WHERE fila = 'A';
UPDATE fila SET fila = 'E' WHERE fila = 'B';
UPDATE fila SET fila = 'F' WHERE fila = 'C';
UPDATE fila SET fila = 'G' WHERE fila = 'G';

INSERT IGNORE INTO fila (fila, capacidadTotal, estado) VALUES
    ('G', 16, 1),
    ('L', 16, 1),
    ('SEGUNDO PATIO', 16, 1);

UPDATE fila SET capacidadTotal = 16;

-- 2) Las plazas numeradas dejan de usarse en este modelo.
DELETE FROM plaza;

-- 3) Tipos de vehículo.
CREATE TABLE IF NOT EXISTS tipovehiculo (
  idTipoVehiculo INT(11) NOT NULL AUTO_INCREMENT,
  tipoVehiculo VARCHAR(15) NOT NULL,
  PRIMARY KEY (idTipoVehiculo),
  UNIQUE KEY uq_tipovehiculo (tipoVehiculo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO tipovehiculo (tipoVehiculo) VALUES
    ('SEDAN'), ('HATCHBACK'), ('CAMIONETA'), ('SUV');

ALTER TABLE vehiculo
  ADD COLUMN idTipoVehiculo INT(11) NULL DEFAULT NULL AFTER idModelo,
  ADD KEY idx_vehiculo_tipo (idTipoVehiculo),
  ADD CONSTRAINT fk_vehiculo_tipo FOREIGN KEY (idTipoVehiculo)
      REFERENCES tipovehiculo (idTipoVehiculo) ON UPDATE CASCADE;

UPDATE vehiculo
SET idTipoVehiculo = (SELECT idTipoVehiculo FROM tipovehiculo WHERE tipoVehiculo = 'HATCHBACK' LIMIT 1)
WHERE idTipoVehiculo IS NULL;

-- 4) Servicios extra reales (se renombran los existentes para conservar FKs).
UPDATE servicioextra SET servicioExtra = 'Lavado', valor = 15000 WHERE idServicioExtra = 1;
UPDATE servicioextra SET servicioExtra = 'Lavado y aspirado', valor = 22000 WHERE idServicioExtra = 2;
UPDATE servicioextra SET servicioExtra = 'Aspirado', valor = 8000 WHERE idServicioExtra = 3;
INSERT IGNORE INTO servicioextra (idServicioExtra, servicioExtra, valor, activo)
VALUES (4, 'Sin servicio', 0, 1);
UPDATE servicioextra SET activo = 1;

-- 5) Regreso programado.
ALTER TABLE ingreso
  ADD COLUMN fechaRegreso DATE NULL DEFAULT NULL AFTER horaInicio,
  ADD COLUMN horaRegreso TIME NULL DEFAULT NULL AFTER fechaRegreso;
