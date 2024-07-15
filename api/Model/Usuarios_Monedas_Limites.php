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
        ], <<<sql
CREATE UNIQUE INDEX usuarios_monedas_limites_id_usuario_id_moneda_uindex
	ON usuarios_monedas_limites (id_usuario, id_moneda);
sql
        );
    }

    public function selectLimits($user_id)
    {
        $sql = <<<sql
SELECT id_moneda,limite,cantidad FROM usuarios_monedas_limites WHERE id_usuario=:id_usuario;
sql;
        $mysql = new MySQL();
        return $mysql->prepare2($sql, [':id_usuario'=> $user_id]);
    }

    /**
     * @param array $row
     * @throws \CoreException
     */
    public function updateLimit(array $row)
    {
        if (!$row['limite']) {
            $sql = <<<sql
DELETE FROM usuarios_monedas_limites WHERE id_usuario=:id_usuario AND id_moneda=:id_moneda;
sql;
            $mysql = new MySQL();
            $mysql->prepare2($sql, [
                ':id_usuario' => $row['id_usuario'],
                ':id_moneda' => $row['id_moneda'],
            ]);
        } else {
            $sql = <<<sql
SELECT * FROM usuarios_monedas_limites WHERE id_usuario=:id_usuario AND id_moneda=:id_moneda;
sql;
            $mysql = new MySQL();
            $row = $row + $mysql->prepare2($sql, [
                    ':id_usuario' => $row['id_usuario'],
                    ':id_moneda' => $row['id_moneda'],
                ])->fetch();

            $sql = <<<sql
REPLACE INTO usuarios_monedas_limites(id_usuario, id_moneda, limite, cantidad) VALUES (:id_usuario, :id_moneda, :limite, :cantidad);
sql;
            $mysql = new MySQL();
            $mysql->prepare2($sql, [
                ':id_usuario' => $row['id_usuario'],
                ':id_moneda' => $row['id_moneda'],
                ':limite' => $row['limite'],
                ':cantidad' => ($row['limite'] * 0.05),
            ]);
        }
    }
}
