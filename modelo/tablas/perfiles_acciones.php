<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 28/feb/2017
 * Time: 01:34 PM
 */
class TablaPerfiles_Acciones extends Tabla
{
    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE _perfiles_acciones(
  id_perfil_accion BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  id_perfil BIGINT(20) NOT NULL,
  id_modulo BIGINT(20) NOT NULL,
  id_accion BIGINT(20) NOT NULL
);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (1, 1, 1001, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (2, 1, 1002, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (3, 1, 1003, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (4, 1, 2001, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (5, 1, 2002, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (6, 1, 2005, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (7, 1, 2003, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (8, 1, 2004, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (9, 1, 2006, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (10, 1, 2007, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (11, 1, 2008, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (12, 1, 2009, 1);
INSERT INTO `_perfiles_acciones` (id_perfil_accion, id_perfil, id_modulo, id_accion) VALUES (13, 1, 2010, 1);
MySQL;
       return $this->multiconsulta($sql);
    }

    function selectAccionesFromModuloAndPerfil($id_modulo, $id_perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  nombre_accion                      accion,
  coalesce(pa.id_perfil = p.id_perfil, 0) estatus
FROM _perfiles_acciones pa
  RIGHT JOIN _acciones a ON a.id_accion = pa.id_accion
  JOIN _perfiles p ON p.id_perfil = pa.id_perfil
WHERE pa.id_perfil = $id_perfil AND id_modulo = $id_modulo;
MySQL;
        return $this->consulta($sql);
    }

    function selectAccionesFromPerfil($id_perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  navegar_modulo                          modulo,
  nombre_accion                      accion,
  coalesce(pa.id_perfil = p.id_perfil, 0) estatus
FROM _perfiles_acciones pa
  RIGHT JOIN _acciones a ON a.id_accion = pa.id_accion
  JOIN _perfiles p ON p.id_perfil = pa.id_perfil
  INNER JOIN _modulos m ON m.id_modulo=pa.id_modulo
WHERE pa.id_perfil = '$id_perfil';
MySQL;
        $consulta = $this->consulta($sql);
        $acciones = $this->query2multiarray($consulta);
        return $acciones;
    }

    function insertPerfilAccion($id_perfil, $id_accion, $id_modulo)
    {
        $sql = /**@lang MySQL */
            <<<MySQL
REPLACE INTO _perfiles_acciones (id_perfil, id_accion, id_modulo) 
VALUES ($id_perfil, $id_accion, $id_modulo);
MySQL;
        $this->consulta($sql);
    }

    function deleteAccionesPerfil($id_perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
DELETE FROM _perfiles_acciones WHERE id_perfil=$id_perfil;
MySQL;
        $this->consulta($sql);
    }
}