<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/07/2017
 * Time: 04:35 PM
 */

class TablaClientes extends Tabla
{

    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE clientes
(
    id_cliente BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nombre_cliente VARCHAR(255) NOT NULL,
    direccion_eth_cliente VARCHAR(45)
);
MySQL;
        $this->consulta($sql);
    }

    public function insertCliente($nombre_cliente)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
INSERT INTO clientes(nombre_cliente) VALUES ('$nombre_cliente')
MySQL;
        return $this->consulta($sql);
    }

    public function selectClienteFromId($id_cliente)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT 
 id_cliente id,
 nombre_cliente nombre,
 direccion_eth_cliente direccionEth
 FROM clientes WHERE id_cliente='$id_cliente';
MySQL;
        return $this->siguiente_registro($this->consulta($sql));
    }

    public function updateDireccionEth($id_cliente, $direccion_eth_cliente)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
UPDATE clientes SET direccion_eth_cliente='$direccion_eth_cliente' WHERE id_cliente='$id_cliente'
MySQL;
        $this->consulta($sql);
    }
}