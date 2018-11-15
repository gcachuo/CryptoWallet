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
    }
}