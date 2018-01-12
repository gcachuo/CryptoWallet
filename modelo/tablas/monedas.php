<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 09/08/2017
 * Time: 10:02 AM
 */

class TablaMonedas extends Tabla
{

    function create_table()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
CREATE TABLE monedas
(
    id_moneda BIGINT(20) PRIMARY KEY NOT NULL,
    nombre_moneda VARCHAR(100) NOT NULL,
    simbolo_moneda VARCHAR(3) NOT NULL,
    book_moneda VARCHAR(7) NOT NULL
);
CREATE UNIQUE INDEX monedas_book_moneda_uindex ON monedas (book_moneda);
CREATE UNIQUE INDEX monedas_nombre_moneda_uindex ON monedas (nombre_moneda);
CREATE UNIQUE INDEX monedas_simbolo_moneda_uindex ON monedas (simbolo_moneda);
MySQL;
        $this->multiconsulta($sql);
    }

    public function selectMonedaFromSimbolo($simbolo_moneda)
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT 
 id_moneda id,
 nombre_moneda nombre,
 simbolo_moneda simbolo,
 book_moneda book
 FROM monedas WHERE simbolo_moneda='$simbolo_moneda'
MySQL;
        return $this->siguiente_registro($this->consulta($sql));
    }

    public function selectMonedas()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT 
 id_moneda id,
 nombre_moneda nombre,
 simbolo_moneda simbolo,
 book_moneda book
 FROM monedas
MySQL;
        return $this->query2multiarray($this->consulta($sql));
    }

    public function selectListaMonedas()
    {
        $sql = /** @lang MySQL */
            <<<MySQL
SELECT 
 simbolo_moneda id,
 nombre_moneda nombre
 FROM monedas
MySQL;
        return $this->query2array($this->consulta($sql),"nombre");
    }
}