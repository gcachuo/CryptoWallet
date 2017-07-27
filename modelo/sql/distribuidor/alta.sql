CREATE TABLE `_acciones`
(
  id_accion          BIGINT AUTO_INCREMENT
    PRIMARY KEY,
  descripcion_accion VARCHAR(100)     NOT NULL
  COMMENT 'palabra en minusculas para manejo en codigo, *no es el nombre de la accion*',
  estatus_accion     BIT DEFAULT b'1' NULL,
  CONSTRAINT acciones_descripcion_accion_uindex
  UNIQUE (descripcion_accion)
);

CREATE TABLE `_modulos`
(
  id_modulo      BIGINT           NOT NULL
    PRIMARY KEY,
  icono_modulo   VARCHAR(100)     NULL,
  padre_modulo   BIGINT           NOT NULL,
  orden_modulo   BIGINT           NOT NULL,
  navegar_modulo VARCHAR(100)     NULL,
  estatus_modulo BIT DEFAULT b'1' NULL
);

CREATE TABLE `_perfiles`
(
  id_perfil         BIGINT AUTO_INCREMENT
    PRIMARY KEY,
  id_usuario_create BIGINT           NOT NULL
  COMMENT 'usuario que creo el perfil',
  nombre_perfil     VARCHAR(100)     NOT NULL,
  estatus_perfil    BIT DEFAULT b'1' NOT NULL,
  CONSTRAINT `_perfiles_nombre_perfil_pk`
  UNIQUE (nombre_perfil)
);

CREATE TABLE `_usuarios`
(
  id_usuario        BIGINT AUTO_INCREMENT
    PRIMARY KEY,
  nombre_usuario    VARCHAR(100)       NULL,
  login_usuario     VARCHAR(50)        NOT NULL,
  password_usuario  VARCHAR(255)       NOT NULL,
  correo_usuario    VARCHAR(255)       NULL,
  estatus_usuario   BIT DEFAULT b'1'   NOT NULL,
  perfil_usuario    BIGINT DEFAULT '1' NOT NULL,
  id_usuario_create BIGINT             NOT NULL
  COMMENT 'usuario que creo el registro',
  CONSTRAINT usuarios_login_usuario_uindex
  UNIQUE (login_usuario)
);

CREATE INDEX usuarios_perfiles_id_perfil_fk
  ON `_usuarios` (perfil_usuario);

CREATE TABLE ciudades
(
  id_ciudad     BIGINT AUTO_INCREMENT
    PRIMARY KEY,
  id_estado     BIGINT       NOT NULL,
  nombre_ciudad VARCHAR(100) NOT NULL
);

CREATE TABLE estados
(
  id_estado     BIGINT AUTO_INCREMENT
    PRIMARY KEY,
  nombre_estado VARCHAR(100) NOT NULL,
  CONSTRAINT estados_nombre_estado_uindex
  UNIQUE (nombre_estado)
);

CREATE TABLE perfiles_acciones
(
  id_perfil_accion BIGINT AUTO_INCREMENT
    PRIMARY KEY,
  id_perfil        BIGINT NOT NULL,
  id_accion        BIGINT NOT NULL,
  id_modulo        BIGINT NOT NULL,
  CONSTRAINT perfiles_acciones_id_accion_id_perfil_id_modulo_pk
  UNIQUE (id_accion, id_perfil, id_modulo)
);
