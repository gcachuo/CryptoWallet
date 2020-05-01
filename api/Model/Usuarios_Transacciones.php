<?php


namespace Model;


use Helper\BitsoOrderPayload;

class Usuarios_Transacciones
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('usuarios_transacciones', [
            new TableColumn('id_usuario_transaccion', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('id_usuario', ColumnTypes::BIGINT, 20, true),
            new TableColumn('id_moneda', ColumnTypes::VARCHAR, 5, true),
            new TableColumn('fecha_usuario_transaccion', ColumnTypes::TIMESTAMP, 0, false, "CURRENT_TIMESTAMP"),
            new TableColumn('costo_usuario_moneda', ColumnTypes::DECIMAL, "15,2", false, "0.00"),
            new TableColumn('cantidad_usuario_moneda', ColumnTypes::DECIMAL, "15,8", true),
        ], <<<sql
alter table usuarios_transacciones
	add constraint usuarios_transacciones_monedas_id_moneda_fk
		foreign key (id_moneda) references monedas (id_moneda)
			on update cascade on delete cascade;

alter table usuarios_transacciones
	add constraint usuarios_transacciones_usuarios_id_usuario_fk
		foreign key (id_usuario) references usuarios (id_usuario)
			on update cascade on delete cascade;
sql
        );
    }

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

    /**
     * @param $user_id
     * @param $id_moneda
     * @param $costo
     * @param BitsoOrderPayload $order
     */
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
