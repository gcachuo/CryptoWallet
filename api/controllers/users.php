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
        $email = isset_get($_POST['email']);
        $password = isset_get($_POST['password']);

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

    function forgotPassword()
    {
        $password_usuario = password_hash(isset_get($_POST['password']), CRYPT_BLOWFISH);
        $correo_usuario = isset_get($_POST['email']);

        if (!$correo_usuario || !$password_usuario) {
            set_error("Fill all the fields.");
        }

        $sql = <<<sql
update usuarios set password_usuario='$password_usuario' where correo_usuario='$correo_usuario';
sql;
        db_query($sql);
        return 'Contraseña reestablecida';
    }

    function fetchAmounts()
    {
        $user_id = decrypt(isset_get($_POST['user']['id']));

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

        $prices = [];
        foreach ($amounts as $key => $amount) {
            if (empty($prices[$amount['book']])) {
                try {
                    $ticker = $bitso->ticker(["book" => $amount['book']]);
                    $prices[$amount['book']] = $ticker->payload->ask;
                } catch (\BitsoAPI\bitsoException $exception) {
                    $prices[$amount['book']] = 0;
                }
            }
            $amount['costo'] = (float)$amount['costo'] > 0 ? (float)$amount['costo'] : 0.01;

            $amounts[$key]['precio'] = $prices[$amount['book']];
            $amounts[$key]['total'] = $amount['cantidad'] * $amounts[$key]['precio'];
            $amounts[$key]['porcentaje'] = (float)$amount['costo'] ? ($amounts[$key]['total'] - $amount['costo']) / $amount['costo'] : 0;
            $amounts[$key]['promedio'] = ((float)$amount['cantidad'] ? $amount['costo'] / $amount['cantidad'] : 0);
        }

        return compact('amounts');
    }

    function fetchClients()
    {
        $user_id = decrypt(isset_get($_POST['user']['id']));

        $sql = <<<sql
select
  u.id_usuario id,
  nombre_usuario nombre,
  nombre_moneda                          moneda,
  sum(costo_usuario_moneda)              costo,
  round(sum(cantidad_usuario_moneda), 8) cantidad,
  concat(ut.id_moneda, '_', par_moneda)  book
from usuarios u
       inner join usuarios_transacciones ut on u.id_usuario = ut.id_usuario
       inner join monedas m on ut.id_moneda = m.id_moneda
where id_cliente = '$user_id'
group by u.id_usuario,m.id_moneda;
sql;
        $clients = db_all_results($sql);

        $bitso = new BitsoAPI\bitso('', '');

        $prices = [];
        foreach ($clients as $key => $client) {
            if (empty($prices[$client['book']])) {
                try {
                    $ticker = $bitso->ticker(["book" => $client['book']]);
                    $prices[$client['book']] = $ticker->payload->ask;
                } catch (\BitsoAPI\bitsoException $exception) {
                    $prices[$client['book']] = 0;
                }
            }
            $clients[$key]['precio'] = $prices[$client['book']];
            $clients[$key]['total'] = $client['cantidad'] * $clients[$key]['precio'];
            $clients[$key]['porcentaje'] = (float)$client['costo'] ? ($clients[$key]['total'] - $client['costo']) / $client['costo'] : 0;
            $clients[$key]['promedio'] = (float)$client['cantidad'] ? $client['cantidad'] / $client['cantidad'] : 0;
        }

        $temp_clients = [];
        foreach ($clients as $key => $client) {
            $temp_clients[$client['id']] = [
                'nombre' => $client['nombre'],
                'costo' => (isset_get($temp_clients[$client['id']]['costo'], 0) + $client['costo']),
                'total' => (isset_get($temp_clients[$client['id']]['total'], 0) + $client['total'])
            ];
        }
        $clients = array_values($temp_clients);

        $wallet = 0;
        foreach ($this->fetchAmounts()['amounts'] as $self) {
            $wallet += $self['total'];
        }

        return compact('clients', 'wallet');
    }
}