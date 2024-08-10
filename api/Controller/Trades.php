<?php

namespace Controller;

use Controller;
use CoreException;
use Helper\Bitso;
use Model\Usuarios_Transacciones;
use System;

class Trades extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'GET' => [
                'data' => 'getTrades',
                'order' => 'getOrder',
                'profit' => 'getProfit',
            ]
        ]);
    }

    /**
     * @return array
     * @throws CoreException
     */
    protected function getProfit(): array
    {
        System::check_value_empty($_GET, ['coin']);
        $user = System::decode_token(USER_TOKEN);
        $user_id = System::decrypt($user['id']);

        $o_usuarios_transacciones = new Usuarios_Transacciones();
        $profit = $o_usuarios_transacciones->selectCalc($user_id, $_GET['coin']);

        return compact('profit');
    }

    /**
     * @return array
     * @throws CoreException
     */
    protected function getOrder(): array
    {
        System::check_value_empty($_GET, ['user_token', 'oid']);
        $user = System::decode_token($_GET['user_token']);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $Bitso = new Bitso($user_id);
        $order = $Bitso->lookupOrder($_GET['oid']);
        $order_trade = $Bitso->orderTrades($_GET['oid']);
        return compact('order', 'order_trade');
    }

    /**
     * @param $idUser
     * @param $coin
     * @return array
     */
    public static function getTradesByCoin($idUser, $coin): array
    {
        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $trades = $Usuarios_Transacciones->selectTrades($idUser, $coin);

        $buy = $sell = 0;
        array_walk($trades, function (&$trade) use (&$buy, &$sell, &$dca) {
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
                $dca = $trade['dca'];
                $trade['date'] = date('Y-m-d H:i', strtotime($trade['date']));
                $trade['trade'] = $trade['price'];
                $trade['cost'] = abs($trade['cost']);
                $trade['quantity'] = abs($trade['quantity']);
            }
        });

        $trades = array_values(array_filter($trades));

        return compact('trades', 'buy', 'sell','dca');
    }

    /**
     * @return array
     * @throws CoreException
     */
    protected function getTrades(): array
    {
        System::check_value_empty($_GET, ['coin']);

        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        [
            'trades' => $trades,
            'buy' => $buy,
            'sell' => $sell,
        ] = $this->getTradesByCoin($user_id, $_GET['coin']);

        return compact('trades', 'buy', 'sell');
    }
}
