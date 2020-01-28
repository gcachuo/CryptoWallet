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

    function selectDiff($fecha, $user_id, $id_moneda)
    {
        $sql = <<<sql
select fecha_usuario_transaccion old,? new,TIMESTAMPDIFF(MINUTE,fecha_usuario_transaccion,?) diff from usuarios_transacciones
where id_usuario=? and id_moneda=? order by id_usuario_transaccion desc
limit 1;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['ssis', $fecha, $fecha, $user_id, $id_moneda]));
    }

    function insertOrder($user_id, $id_moneda, $costo, $order)
    {
        $sql = <<<sql
insert into usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda,cantidad_usuario_moneda) VALUES (?,?,?,?);
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['isdd', $user_id, $id_moneda, -$costo, -$order->original_amount]);

        $sql = <<<sql
insert into usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda,cantidad_usuario_moneda) VALUES (?,'mxn',?,?);
sql;
        $mysql->prepare($sql, ['idd', $user_id, $costo, $costo]);
    }
}
