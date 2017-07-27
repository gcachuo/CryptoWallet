<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 07/mar/2017
 * Time: 12:25 PM
 */
class TablaAcciones extends bd
{
    function create_table()
    {
        // TODO: Implement create_table() method.
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