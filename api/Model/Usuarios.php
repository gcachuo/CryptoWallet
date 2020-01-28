<?php


namespace Model;


class Usuarios
{

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
}
