<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 04/08/2017
 * Time: 04:33 PM
 */

class TablaUsuario_Llaves extends bd
{

    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE usuario_llaves
(
    id_usuario_llave BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    id_usuario BIGINT(20) NOT NULL,
    api_key VARCHAR(255),
    api_secret VARCHAR(255)
);
CREATE UNIQUE INDEX usuario_llaves_id_usuario_uindex ON usuario_llaves (id_usuario);
MySQL;
        $this->multiconsulta($sql);
    }

    function selectApiKey($id_usuario){
        $sql=/** @MySQL */<<<MySQL
select api_key apiKey, api_secret apiSecret from usuario_llaves where id_usuario='$id_usuario'
MySQL;
return $this->siguiente_registro($this->consulta($sql));
    }
}