<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 07/mar/2017
 * Time: 12:25 PM
 */
class TablaAcciones extends Tabla
{
    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE `_acciones`
(
    id_accion bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre_accion varchar(100) NOT NULL,
    estatus_accion bit(1) DEFAULT b'1'
);
INSERT INTO `_acciones` (id_accion, nombre_accion, estatus_accion) VALUES (1, 'accesar', true);
MySQL;
        return $this->multiconsulta($sql);
    }

    function selectAcciones()
    {
        $sql = /** @lang */
            <<<MySQL
SELECT
  id_accion          id,
  descripcion_accion nombre
FROM `_acciones`
WHERE estatus_accion = TRUE AND descripcion_accion != 'accesar';
MySQL;

        return $this->consulta($sql);
    }
}