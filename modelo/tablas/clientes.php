<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/07/2017
 * Time: 04:35 PM
 */

class TablaClientes extends bd
{

    function create_table()
    {
        $sql=/** @lang MySQL */ <<<MySQL
CREATE table clientes(
id_cliente BIGINT(20) PRIMARY KEY AUTO_INCREMENT NOT NULL,
nombre_cliente VARCHAR(255) NOT NULL 
);
MySQL;
$this->consulta($sql);
    }

    public function insertCliente($nombre_cliente)
    {
        $sql=/** @lang MySQL */ <<<MySQL
insert into clientes(nombre_cliente) VALUES ('$nombre_cliente')
MySQL;
return $this->consulta($sql);
    }
}