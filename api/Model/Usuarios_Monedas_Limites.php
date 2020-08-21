<?php


namespace Model;


class Usuarios_Monedas_Limites
{
    public function __construct()
    {
        $mysql = new MySQL();
        $mysql->create_table('usuarios_monedas_limites', [
            new TableColumn('id_usuario_moneda_limite', ColumnTypes::BIGINT, 20, true, null, true, true),
            new TableColumn('id_usuario', ColumnTypes::BIGINT, 20, true),
            new TableColumn('id_moneda', ColumnTypes::VARCHAR, 5, true),
            new TableColumn('limite', ColumnTypes::DECIMAL, '15,2', true),
            new TableColumn('cantidad', ColumnTypes::DECIMAL, '15,2', false),
        ],<<<sql
create unique index usuarios_monedas_limites_id_usuario_id_moneda_uindex
	on usuarios_monedas_limites (id_usuario, id_moneda);
sql
);
    }

    public function selectLimits($user_id)
    {
        $sql = <<<sql
SELECT id_moneda,limite,cantidad FROM usuarios_monedas_limites WHERE id_usuario=?;
sql;
        $mysql = new MySQL();
        return $mysql->prepare($sql, ['i', $user_id]);
    }
}
