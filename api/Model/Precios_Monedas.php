<?php


namespace Model;


class Precios_Monedas
{
    function selectFallbackPrice(array $amount)
    {
        $sql = <<<sql
SELECT precio_moneda precio FROM precios_monedas WHERE id_moneda=:id_moneda ORDER BY fecha_precio_moneda DESC 
sql;
        $mysql = new MySQL();
        return $mysql->prepare2($sql, [':id_moneda' => $amount['idMoneda']])->fetch()['precio'] ?: 0;
    }
}
