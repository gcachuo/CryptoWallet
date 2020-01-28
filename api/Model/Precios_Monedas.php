<?php


namespace Model;


class Precios_Monedas
{
    function selectFallbackPrice(array $amount)
    {
        $sql = <<<sql
select precio_moneda precio from precios_monedas where id_moneda=? order by fecha_precio_moneda desc 
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['s', $amount['idMoneda']]))['precio'] ?: 0;
    }
}
