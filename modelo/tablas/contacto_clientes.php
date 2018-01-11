<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/07/2017
 * Time: 04:13 PM
 */

class TablaContacto_Clientes extends Tabla
{
    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE contacto_clientes(
id_contacto_cliente BIGINT(20) AUTO_INCREMENT PRIMARY KEY NOT NULL,
id_cliente BIGINT(20),
telefono_cliente VARCHAR(50),
correo_cliente VARCHAR(100)
);
MySQL;
        return $this->consulta($sql);
    }

    function modify_table()
    {
        $sql = <<<MySQL
alter table contacto_clientes add COLUMN id_cliente BIGINT(20), add COLUMN telefono_cliente VARCHAR(50);
MySQL;
        return $this->consulta($sql);
    }

    function selectCountCorreo($correo_cliente)
    {
        $sql = /**  @lang MySQL */
            <<<MySQL
SELECT count(*) count FROM contacto_clientes WHERE correo_cliente='$correo_cliente'
MySQL;
        return $this->siguiente_registro($this->consulta($sql))->count;
    }

    public function insertContactoCliente($id_cliente, $telefono_cliente, $correo_cliente)
    {
        $sql = /**  @lang MySQL */
            <<<MySQL
INSERT INTO contacto_clientes (id_cliente,telefono_cliente,correo_cliente) VALUES ('$id_cliente','$telefono_cliente','$correo_cliente')
MySQL;
        $this->consulta($sql);
    }
}