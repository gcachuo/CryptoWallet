<?php


namespace Model;


class Usuarios_Monedas_Limites
{

    public function selectLimits($user_id)
    {
        $sql = <<<sql
select id_moneda,limite,cantidad from usuarios_monedas_limites where id_usuario=?;
sql;
        $mysql = new MySQL();
        return $mysql->prepare($sql, ['i', $user_id]);
    }
}
