<?php


namespace Controller;


use BitsoAPI\bitso;
use BitsoAPI\bitsoException;
use Controller;
use HTTPStatusCodes;
use JsonResponse;
use Model\Precios_Monedas;
use Model\Usuarios_Keys;
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
                'sellCoin' => 'sellCoin',
            ]
        ]);
    }

    protected function sellCoin()
    {
        $user_id = System::decrypt(System::isset_get($_POST['user']['id']));
        $id_moneda = $_POST['coin']['idMoneda'];
        $costo = $_POST['total'];
        $fecha = date('Y-m-d H:i:s');

        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $diff = $Usuarios_Transacciones->selectDiff($fecha, $user_id, $id_moneda);

        if ($diff['diff'] == 0) {
            JsonResponse::sendResponse(['message' => 'Duplicated transaction.']);
        }

        $Usuarios_Keys = new Usuarios_Keys();
        $keys = $Usuarios_Keys->selectKeys($user_id);

        $api_key = System::decrypt($keys['api_key']);
        $api_secret = System::decrypt($keys['api_secret']);

        $bitso = new bitso($api_key, $api_secret);
        $place_order = $bitso->place_order(['book' => "{$id_moneda}_mxn", 'side' => 'sell', 'type' => 'market', 'minor' => $costo]);
        sleep(10);
        $orders = $bitso->lookup_order([$place_order->payload->oid]);

        if (empty($orders->payload)) {
            $bitso->cancel_order(['order_id' => '']);
            JsonResponse::sendResponse(['message' => 'Error placing order.'], HTTPStatusCodes::ServiceUnavailable);
        }

        foreach ($orders->payload as $order) {
            $Usuarios_Transacciones->insertOrder($user_id, $id_moneda, $costo, $order);

            if ($order->original_value == 0) {
                JsonResponse::sendResponse(['message' => 'Error. Inserting zero.']);
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
