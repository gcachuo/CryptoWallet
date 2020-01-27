<?php


namespace Model;


class Usuarios_Transacciones
{
    function selectAmounts($user_id)
    {
        $sql = <<<sql
select
  m.id_moneda idMoneda,
  nombre_moneda moneda,
  sum(costo_usuario_moneda)              costo,
  round(sum(cantidad_usuario_moneda), 8) cantidad,
  concat(ut.id_moneda,'_',par_moneda) book
from usuarios_transacciones ut
inner join monedas m on ut.id_moneda = m.id_moneda
where id_usuario = ?
group by ut.id_moneda;
sql;

        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->prepare($sql, ['i', $user_id]));
    }
}
