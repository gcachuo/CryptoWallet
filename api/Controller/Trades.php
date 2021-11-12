<?php

namespace Controller;

use Controller;
use CoreException;
use Model\Usuarios_Transacciones;
use System;

class Trades extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'GET' => [
                'data' => 'getTrades'
            ]
        ]);
    }

    /**
     * @return array
     * @throws CoreException
     */
    protected function getTrades(): array
    {
        System::check_value_empty($_GET, ['coin']);

        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $trades = $Usuarios_Transacciones->selectTrades(1, $_GET['coin']);

        $buy = $sell = 0;
        array_walk($trades, function (&$trade) use (&$buy, &$sell) {
            if (!$trade['price'] || $trade['price'] <= 0) {
                $trade = null;
            } else {
                if (+$trade['cost'] > 0) {
                    $trade['buy'] = $trade['price'];
                    $trade['type'] = 'buy';
                    $buy = $trade['price'];
                } elseif (+$trade['cost'] < 0) {
                    $trade['sell'] = $trade['price'];
                    $trade['type'] = 'sell';
                    $sell = $trade['price'];
                }
                $trade['date'] = date('Y-m-d H:i', strtotime($trade['date']));
                $trade['trade'] = $trade['price'];
                $trade['cost'] = abs($trade['cost']);
                $trade['quantity'] = abs($trade['quantity']);
            }
        });

        $trades = array_values(array_filter($trades));

        return compact('trades', 'buy', 'sell');
    }
}
