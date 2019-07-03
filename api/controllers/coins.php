<?php


class coins
{
    function getTotal()
    {
        $costo = isset_get($_POST['costo']);
        $precio = isset_get($_POST['precio']);
        $cantidad = isset_get($_POST['cantidad']);

        $fee = isset_get($_POST['fee'], 0.5);

        if (!$precio) {
            return false;
        }

        switch (true) {
            case empty($cantidad):
                $cantidad = round($costo / $precio, 8);
                $total['coin'] = round($cantidad - $cantidad / 100 * $fee, 8);
                break;
            case empty($costo):
                $costo = $cantidad * $precio;
                $total['fiat'] = $costo - $precio * $fee;
                break;
        }

        return compact('total');
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
        }
        return true;
    }
}