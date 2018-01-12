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
    id_accion BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre_accion VARCHAR(100) NOT NULL,
    estatus_accion BIT(1) DEFAULT b'1'
);
INSERT INTO `_acciones` (id_accion, nombre_accion, estatus_accion) VALUES (1, 'accesar', TRUE);
MySQL;
        return $this->multiconsulta($sql);
    }

    function selectAcciones()
    {
        $sql = /** @lang */
            <<<MySQL
SELECT
  id_accion          id,
  nombre_accion nombre
FROM `_acciones`
WHERE estatus_accion = TRUE AND nombre_accion != 'accesar';
MySQL;
        return $this->query2multiarray($this->consulta($sql));
    }
}