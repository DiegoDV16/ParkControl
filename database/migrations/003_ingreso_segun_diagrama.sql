-- ==========================================================================
-- ParkControl · 003_ingreso_segun_diagrama.sql
-- --------------------------------------------------------------------------
-- Alinea la tabla `ingreso` con el diagrama ParkControl.drawio:
--   · Elimina las columnas heredadas idPlaza e idTarifa.
--   · Elimina las tablas `plaza` y `tarifa`, que no existen en el diagrama.
--   · Ajusta horaTermino a DATETIME, como indica el diagrama.
--
-- Conserva los ingresos existentes: las columnas nuevas tienen valor nulo
-- o se recalculan a partir de fechaTermino.
-- ==========================================================================

ALTER TABLE ingreso DROP FOREIGN KEY fk_ingreso_plaza;
ALTER TABLE ingreso DROP COLUMN idPlaza;

ALTER TABLE ingreso DROP FOREIGN KEY fk_ingreso_tarifa;
ALTER TABLE ingreso DROP COLUMN idTarifa;

DROP TABLE IF EXISTS plaza;
DROP TABLE IF EXISTS tarifa;

-- horaTermino pasa de TIME a DATETIME: se combina con fechaTermino.
UPDATE ingreso
SET horaTermino = TIMESTAMP(fechaTermino, horaTermino)
WHERE fechaTermino IS NOT NULL AND horaTermino IS NOT NULL;

ALTER TABLE ingreso MODIFY COLUMN horaTermino DATETIME NULL DEFAULT NULL;
