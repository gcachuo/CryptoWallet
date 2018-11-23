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

    function fetchAmounts()
    {
        $user_id = decrypt($_POST['user']['id']);

        $sql = <<<sql
select
  nombre_moneda moneda,
  sum(costo_usuario_moneda)              costo,
  round(sum(cantidad_usuario_moneda), 8) cantidad,
  concat(ut.id_moneda,'_',par_moneda) book
from usuarios_transacciones ut
inner join monedas m on ut.id_moneda = m.id_moneda
where id_usuario = '$user_id'
group by ut.id_moneda;
sql;
        $amounts = db_all_results($sql);

        $bitso = new BitsoAPI\bitso('', '');

        foreach ($amounts as $key => $amount) {
            $ticker=$bitso->ticker(["book"=>$amounts[$key]['book']]);
            $amounts[$key]['precio'] = $ticker->payload->ask;
            $amounts[$key]['total']=$amount['cantidad']*$amounts[$key]['precio'];
            $amounts[$key]['porcentaje']=($amounts[$key]['total']-$amount['costo'])/$amount['costo'];
            $amounts[$key]['promedio']=$amount['costo']/$amount['cantidad'];
        }

        return compact('amounts');
    }

    function forgotPassword()
    {
        $password_usuario = password_hash($_POST['password'], CRYPT_BLOWFISH);
        $correo_usuario = ($_POST['email']);

        $sql = <<<sql
update usuarios set password_usuario='$password_usuario' where correo_usuario='$correo_usuario';
sql;
        db_query($sql);
        return 'Contraseña reestablecida';
    }
}