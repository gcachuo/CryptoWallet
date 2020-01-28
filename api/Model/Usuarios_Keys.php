<?php


namespace Model;


class Usuarios_Keys
{
    function selectKeys($user_id)
    {
        $sql = <<<sql
select api_key,api_secret from usuarios_keys where id_usuario=?;
sql;
        $mysql = new MySQL();
        return $mysql->fetch_single($mysql->prepare($sql, ['i', $user_id]));
    }
}
