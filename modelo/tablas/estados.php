<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 09/mar/2017
 * Time: 05:06 PM
 */
class TablaEstados extends Tabla
{
    function create_table()
    {
        $sql = <<<MySQL
CREATE TABLE estados
(
    id_estado BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre_estado VARCHAR(100) NOT NULL
);
CREATE UNIQUE INDEX estados_nombre_estado_uindex ON estados (nombre_estado);
MySQL;
        return $this->multiconsulta($sql);
    }

    function selectEstados()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  id_estado     idEstado,
  nombre_estado nombreEstado
FROM estados
MySQL;

        return $this->consulta($sql);
    }
}