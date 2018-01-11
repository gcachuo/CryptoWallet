<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/feb/2017
 * Time: 04:31 PM
 */
class TablaPerfiles extends Tabla
{
    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE _perfiles(
  id_perfil BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  nombre_perfil VARCHAR(100) NOT NULL,
  id_usuario BIGINT(20) NOT NULL DEFAULT 1 COMMENT 'Creador del perfil',
  estatus_perfil BIT(1) DEFAULT b'1'
);
CREATE UNIQUE INDEX `_perfiles_nombre_perfil_uindex` ON `_perfiles` (nombre_perfil);
INSERT INTO _perfiles (id_perfil, nombre_perfil, id_usuario, estatus_perfil) VALUES (1, 'Administrador', 1, TRUE);
MySQL;
        return $this->multiconsulta($sql);
    }

    function selectPerfiles()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  id_perfil     idPerfil,
  nombre_perfil nombrePerfil
FROM _perfiles
WHERE estatus_perfil = TRUE AND id_perfil > 0
MySQL;

        return $this->consulta($sql);
    }

    function insertPerfil($nombre_perfil, $id_usuario_create)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
REPLACE INTO `_perfiles`(nombre_perfil,id_usuario_create) VALUES ('$nombre_perfil',$id_usuario_create)
MySQL;
        $this->consulta($sql);
    }

    function selectLastInsertedPerfil()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT id_perfil idPerfil
FROM `_perfiles`
ORDER BY 1 DESC
LIMIT 1;
MySQL;
        $registro = $this->siguiente_registro($this->consulta($sql));
        $idPerfil = $registro->idPerfil;
        return $idPerfil;
    }

    function selectNombrePerfilFromId($id_perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT nombre_perfil nombrePerfil
FROM `_perfiles`
WHERE id_perfil = $id_perfil
MySQL;
        $registro = $this->siguiente_registro($this->consulta($sql));
        $nombrePerfil = $registro->nombrePerfil;
        return $nombrePerfil;
    }

    function updateNombrePerfil($id_perfil, $nombre_perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
UPDATE `_perfiles` SET nombre_perfil='$nombre_perfil' WHERE id_perfil=$id_perfil;
MySQL;
        $this->consulta($sql);
    }

    function deletePerfil($id_perfil)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
UPDATE `_perfiles` SET estatus_perfil=FALSE WHERE id_perfil=$id_perfil
MySQL;
        $this->consulta($sql);
    }
}