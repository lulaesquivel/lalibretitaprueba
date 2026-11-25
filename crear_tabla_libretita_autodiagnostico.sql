-- La Libretita — tabla del autodiagnóstico público.
-- Correr UNA vez en phpMyAdmin (Hostinger → Bases de datos → phpMyAdmin),
-- en la misma base que usa config.php. No toca la tabla `respuestas` de Emprendo Norte.

CREATE TABLE IF NOT EXISTS libretita_autodiagnostico (
  id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre               VARCHAR(200) NOT NULL,
  email                VARCHAR(200) NULL,
  fecha                DATETIME NOT NULL,
  perfil               VARCHAR(120) NOT NULL DEFAULT '',
  nivel_organizacion   DECIMAL(3,1) NOT NULL DEFAULT 0,
  nivel_administracion DECIMAL(3,1) NOT NULL DEFAULT 0,
  nivel_presupuestos   DECIMAL(3,1) NOT NULL DEFAULT 0,
  nivel_equipo         DECIMAL(3,1) NOT NULL DEFAULT 0,
  nivel_ventas         DECIMAL(3,1) NOT NULL DEFAULT 0,
  nivel_comunicacion   DECIMAL(3,1) NOT NULL DEFAULT 0,
  nivel_tecnologia     DECIMAL(3,1) NOT NULL DEFAULT 0,
  comunicacion_items   TEXT,
  tecnologia_items     TEXT,
  ip                   VARCHAR(45) NULL,
  creado               TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_fecha (fecha),
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
