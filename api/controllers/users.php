<?php

use BitsoAPI\bitso;

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
  m.id_moneda idMoneda,
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
                    $prices[$amount['book']] = round(($ticker->payload->ask + $ticker->payload->bid) / 2, 2);
                } catch (\BitsoAPI\bitsoException $exception) {
                    $sql = <<<sql
select precio_moneda precio from precios_monedas where id_moneda='$amount[idMoneda]' order by fecha_precio_moneda desc 
sql;
                    $fallback = db_result($sql)['precio'] ?: 0;
                    $prices[$amount['book']] = (double)$fallback;
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
  m.id_moneda                            idMoneda,
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
                    $sql = <<<sql
select precio_moneda precio from precios_monedas where id_moneda='$client[idMoneda]' order by fecha_precio_moneda desc 
sql;
                    $fallback = db_result($sql)['precio'] ?: 0;
                    $prices[$client['book']] = $fallback;
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
                'total' => (isset_get($temp_clients[$client['id']]['total'], 0) + $client['total']),
                'monedas' => isset_get($temp_clients[$client['id']]['monedas'], [])
            ];
            $temp_clients[$client['id']]['monedas'][$client['idMoneda']] = $client['cantidad'];
        }
        $clients = array_values($temp_clients);

        $wallet['total'] = 0;
        foreach ($this->fetchAmounts()['amounts'] as $self) {
            $wallet['monedas'][$self['idMoneda']] = $self['cantidad'];
            $wallet['total'] += $self['total'];
        }

        return compact('clients', 'wallet');
    }

    function addTransaction()
    {
        $id_usuario = decrypt(isset_get($_POST['user']['id']));
        $id_moneda = isset_get($_POST['moneda']);
        $costo_usuario_moneda = isset_get($_POST['costo'], 0);
        $cantidad_usuario_moneda = isset_get($_POST['cantidad'], 0);

        if (!$id_usuario || !$id_moneda) {
            return false;
        }

        $sql = <<<sql
INSERT INTO usuarios_transacciones (id_usuario, id_moneda, costo_usuario_moneda, cantidad_usuario_moneda) VALUES ($id_usuario, '$id_moneda', $costo_usuario_moneda, $cantidad_usuario_moneda);
sql;
        db_query($sql);

        return true;
    }

    function getTransactions()
    {
        $id_usuario = decrypt(isset_get($_POST['user']['id']));
        $id_moneda = isset_get($_POST['moneda']);
        $operacion = isset_get($_POST['operacion']);
        if (!$id_usuario) {
            return false;
        }

        $sql = <<<sql
select date(fecha_usuario_transaccion)                           fecha,
       if(sign(cantidad_usuario_moneda) = -1, 'Venta', 'Compra') operacion,
       nombre_moneda                                             moneda,
       cantidad_usuario_moneda                                   cantidad,
       costo_usuario_moneda                                      costo,
       round(costo_usuario_moneda / cantidad_usuario_moneda, 2)  precio
from usuarios_transacciones
         inner join monedas m on usuarios_transacciones.id_moneda = m.id_moneda
where id_usuario = 1
  and if('$id_moneda' <> '', m.id_moneda = '$id_moneda', true)
  and if('$operacion' <> '', if('$operacion' = 'venta', sign(cantidad_usuario_moneda) = -1, sign(cantidad_usuario_moneda) = 1),
         true)
order by fecha_usuario_transaccion desc;
sql;

        $transactions = db_all_results($sql);

        return compact('transactions');
    }

    function sellCoin()
    {
        $user_id = decrypt(isset_get($_POST['user']['id']));
        $id_moneda = $_POST['coin']['idMoneda'];
        $costo = $_POST['total'];

        $sql = <<<sql
select api_key,api_secret from usuarios_keys where id_usuario=$user_id;
sql;
        $keys = db_result($sql);
        $api_key = decrypt($keys['api_key']);
        $api_secret = decrypt($keys['api_secret']);

        $bitso = new BitsoAPI\bitso($api_key, $api_secret);
        $place_order = $bitso->place_order(['book' => "{$id_moneda}_mxn", 'side' => 'sell', 'type' => 'market', 'minor' => $costo]);
        $orders = $bitso->lookup_order([$place_order->payload->oid]);
        foreach ($orders->payload as $order) {
            $sql = <<<sql
insert into usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda,cantidad_usuario_moneda) VALUES ($user_id,'$id_moneda',-$costo,-$order->original_value);
sql;
            db_query($sql);
            $sql = <<<sql
insert into usuarios_transacciones(id_usuario, id_moneda, costo_usuario_moneda,cantidad_usuario_moneda) VALUES ($user_id,'mxn',$costo,$costo);
sql;
            db_query($sql);
        }
        return true;
    }
}