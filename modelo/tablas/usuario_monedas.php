<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 04/08/2017
 * Time: 05:32 PM
 */

class TablaUsuario_Monedas extends bd
{

    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE usuario_monedas
(
    id_usuario_monedas BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_usuario BIGINT(20) NOT NULL,
    id_moneda BIGINT(20) NOT NULL,
    cantidad_usuario_moneda DECIMAL(10,8) NOT NULL,
    costo_usuario_moneda DECIMAL(10,2)
);
MySQL;
        $this->multiconsulta($sql);
    }

    public function selectCantidad($id_usuario, $id_moneda)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT cantidad_usuario_moneda cantidad FROM usuario_monedas WHERE id_usuario='$id_usuario' AND id_moneda='$id_moneda'
MySQL;
        return $this->siguiente_registro($this->consulta($sql))->cantidad;
    }

    public function selectCosto($id_usuario, $id_moneda)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT costo_usuario_moneda costo FROM usuario_monedas WHERE id_usuario='$id_usuario' AND id_moneda='$id_moneda'
MySQL;
        return $this->siguiente_registro($this->consulta($sql))->costo;
    }

    public function updateUsuarioMoneda($id_usuario, $id_moneda, $cantidad_usuario_moneda, $costo_usuario_moneda)
    {
        $cantidad_usuario_moneda = $cantidad_usuario_moneda ?: 0;
        $sql = /** @lang MySQL */
            <<<MySQL
REPLACE INTO usuario_monedas(id_usuario_monedas,id_usuario, id_moneda, cantidad_usuario_moneda, costo_usuario_moneda) VALUES 
(id_usuario_monedas,'$id_usuario','$id_moneda','$cantidad_usuario_moneda','$costo_usuario_moneda')
MySQL;
        $this->consulta($sql);
    }
}