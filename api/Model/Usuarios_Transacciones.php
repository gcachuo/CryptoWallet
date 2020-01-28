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
select fecha_usuario_transaccion old,'$fecha' new,TIMESTAMPDIFF(MINUTE,fecha_usuario_transaccion,'$fecha') diff from usuarios_transacciones
where id_usuario=$user_id and id_moneda='$id_moneda' order by id_usuario_transaccion desc
limit 1;
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['sis', $fecha, $user_id, $id_moneda]);
    }
}
