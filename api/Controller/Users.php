<?php


namespace Controller;


use BitsoAPI\bitso;
use BitsoAPI\bitsoException;
use Controller;
use Model\Precios_Monedas;
use Model\Usuarios_Transacciones;
use System;

class Users extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'POST' => [
                'fetchAmounts' => 'fetchAmounts'
            ]
        ]);
    }

    function fetchAmounts()
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
