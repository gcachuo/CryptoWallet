<?php


namespace Model;


class Usuarios
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('usuarios', [
            new TableColumn('id_usuario', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('id_cliente', ColumnTypes::BIGINT, 20, false),
            new TableColumn('perfil_usuario', ColumnTypes::INTEGER, 11, false, 1),
            new TableColumn('nombre_usuario', ColumnTypes::VARCHAR, 100, true),
            new TableColumn('correo_usuario', ColumnTypes::VARCHAR, 100, true),
            new TableColumn('password_usuario', ColumnTypes::VARCHAR, 255, true),
            new TableColumn('last_login_usuario', ColumnTypes::DATETIME, 0, false),
        ],<<<sql
alter table usuarios
	add constraint usuarios_usuarios_id_usuario_fk
		foreign key (id_cliente) references usuarios (id_usuario)
			on update cascade on delete set null;
create unique index usuarios_correo_usuario_uindex on usuarios (correo_usuario);
sql
);
    }

    public function selectPassword($email)
    {
        $sql = <<<sql
select password_usuario password from usuarios where correo_usuario=?
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['s', $email]))['password'];
    }

    function selectUser($email)
    {
        $sql = <<<sql
select id_usuario id, nombre_usuario nombre, correo_usuario correo, perfil_usuario perfil
from usuarios
where correo_usuario=?
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['s', $email]));
    }

    public function updateLastLogin($user_id)
    {
        $sql = <<<sql
update usuarios set last_login_usuario=NOW() where id_usuario=?
sql;
        $mysql = new MySQL();
        $mysql->prepare($sql, ['i', $user_id]);
    }

    function selectClients($user_id)
    {
        $sql = <<<sql
select
  u.id_usuario id,
  nombre_usuario nombre,
  m.id_moneda                            idMoneda,
  nombre_moneda                          moneda,
  sum(costo_usuario_moneda)              costo,
  round(sum(cantidad_usuario_moneda), 8) cantidad,
  concat(ut.id_moneda, '_', par_moneda)  book
from usuarios u
       inner join usuarios_transacciones ut on u.id_usuario = ut.id_usuario
       inner join monedas m on ut.id_moneda = m.id_moneda
where id_cliente = ?
group by u.id_usuario,m.id_moneda;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_all($mysql->prepare($sql, ['i', $user_id]));
    }
}
