<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/feb/2017
 * Time: 05:08 PM
 */
class TablaModulos extends bd
{
    function create_table()
    {
        $sql = <<<MySQL
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
INSERT INTO `_modulos` (id_modulo, icono_modulo, padre_modulo, orden_modulo, navegar_modulo, estatus_modulo) VALUES (2001, null, 2000, 1, 'config', true);
INSERT INTO `_modulos` (id_modulo, icono_modulo, padre_modulo, orden_modulo, navegar_modulo, estatus_modulo) VALUES (3001, null, 3000, 1, 'wallet', true);
INSERT INTO `_modulos` (id_modulo, icono_modulo, padre_modulo, orden_modulo, navegar_modulo, estatus_modulo) VALUES (4001, null, 4000, 1, 'orders', true);
MySQL;
        return $this->multiconsulta($sql);
    }

    /**
     * @return mysqli_result|null
     */
    function selectModulos()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  id_modulo    idModulo,
  padre_modulo padreModulo,
  orden_modulo ordenModulo,
  icono_modulo iconoModulo,
  navegar_modulo navegarModulo
FROM _modulos
WHERE estatus_modulo = TRUE
order by padre_modulo, orden_modulo;
MySQL;
        return $this->consulta($sql);
    }

    function selectModulosFromParent($id)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT
  id_modulo    idModulo,
  padre_modulo padreModulo,
  orden_modulo ordenModulo,
  icono_modulo iconoModulo,
  navegar_modulo navegarModulo
FROM _modulos
WHERE estatus_modulo = TRUE AND padre_modulo=$id;
MySQL;
        return $this->consulta($sql);
    }

    function selectNombreModuloFromId($id_modulo)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT navegar_modulo nombreModulo FROM `_modulos` WHERE id_modulo='$id_modulo';
MySQL;
        $registro = $this->siguiente_registro($this->consulta($sql));
        $nombreModulo = $registro->nombreModulo;
        return $nombreModulo;
    }
}