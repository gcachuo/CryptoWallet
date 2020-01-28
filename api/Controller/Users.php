<?php


namespace Controller;


use BitsoAPI\bitso;
use BitsoAPI\bitsoException;
use Controller;
use Model\Precios_Monedas;
use Model\Usuarios_Monedas_Limites;
use Model\Usuarios_Transacciones;
use System;

class Users extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'POST' => [
                'fetchAmounts' => 'fetchAmounts',
                'fetchCoinLimits' => 'fetchCoinLimits',
            ]
        ]);
    }

    protected function sellCoin()
    {
        $user_id = System::decrypt(System::isset_get($_POST['user']['id']));
        $id_moneda = $_POST['coin']['idMoneda'];
        $costo = $_POST['total'];
        $fecha = date('Y-m-d H:i:s');

        

        if ($diff['diff'] == 0) {
            error_log('Duplicated transaction.');
            exit;
        }

        $sql = <<<sql
select api_key,api_secret from usuarios_keys where id_usuario=$user_id;
sql;
        $keys = db_result($sql);
        $api_key = decrypt($keys['api_key']);
        $api_secret = decrypt($keys['api_secret']);

        $bitso = new BitsoAPI\bitso($api_key, $api_secret);
        $place_order = $bitso->place_order(['book' => "{$id_moneda}_mxn", 'side' => 'sell', 'type' => 'market', 'minor' => $costo]);
        sleep(10);
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

            if ($order->original_value == 0) {
                set_error("Error. Inserting zero.");
            }
        }
        return true;
    }

    protected function fetchCoinLimits()
    {
        $user_id = System::decrypt(System::isset_get($_POST['user']['id']));

        $Usuarios_Monedas_Limites = new Usuarios_Monedas_Limites();
        $dbresults = $Usuarios_Monedas_Limites->selectLimits($user_id);

        $sell = [];
        foreach ($dbresults as $result) {
            $sell[$result['id_moneda']] = [
                'threshold' => $result['limite'],
                'amount' => $result['cantidad']
            ];
        }
        return compact('sell');
    }

    protected function fetchAmounts()
    {
        $user_id = System::decrypt(System::isset_get($_POST['user']['id']));

        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $amounts = $Usuarios_Transacciones->selectAmounts($user_id);

        $bitso = new bitso('', '');

        $prices = [];
        foreach ($amounts as $key => $amount) {
            if (empty($prices[$amount['book']])) {
                try {
                    $ticker = $bitso->ticker(["book" => $amount['book']]);
                    $prices[$amount['book']] = round(($ticker->payload->ask + $ticker->payload->bid) / 2, 2);
                } catch (bitsoException $exception) {
                    $Precios_Monedas = new Precios_Monedas();
                    $fallback = $Precios_Monedas->selectFallbackPrice($amount);
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
}
