<?php
/**
 * Created by PhpStorm.
 * User: memo
 * Date: 15/11/18
 * Time: 05:27 PM
 */

class users
{
    function signIn()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!$email || !$password) {
            set_error("Fill all the fields.");
        }

        $sql = <<<sql
select password_usuario password from usuarios where correo_usuario='$email'
sql;

        $hash = db_result($sql)['password'];
        if (!password_verify($password, $hash)) {
            set_error("El usuario o la contraseña son incorrectos.");
        }

        $sql = <<<sql
select id_usuario id, nombre_usuario nombre, correo_usuario correo, perfil_usuario perfil
from usuarios
where correo_usuario='$email'
sql;
        $user = db_result($sql);

        $sql = <<<sql
update usuarios set last_login_usuario=NOW() where id_usuario='$user[id]'
sql;
        db_query($sql);

        $user['id'] = encrypt($user['id']);

        return compact('user');
    }
}