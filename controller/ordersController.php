<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 04:55 PM
 */
class ordersController
{
    public $order;

    function __construct()
    {
        $this->setLastOrder();
    }

    function setLastOrder()
    {
        $key = Config::$key;
        Config::generateSignature($nonce, $signature);
        $keys = array("key" => $key, "nonce" => $nonce, "signature" => $signature);

        $orders = Config::request("https://api.bitso.com/v2/open_orders?book=btc_mxn", $keys);
        if ($orders[0]->type == "1") {
            Config::generateSignature($nonce, $signature);

            $order = $orders[0]->amount * -1 . " <span id='noalert'>" . round($orders[0]->amount * $orders[0]->price, 2) . "</span> " . $orders[0]->price . "<button onclick='cancel(\"{$orders[0]->id}\", \"$key\", \"$nonce\", \"$signature\")'>Cancel</button>";
        } elseif ($orders[0]->type == "0") {
            $orderMxn = -round($orders[0]->amount * $orders[0]->price, 2);
            $order = <<<HTML
<td>{$orders[0]->amount}</td>
<td>$orderMxn</td>
<td>{$orders[0]->price}</td>
<td><button class="btn btn-default" onclick='cancel("{$orders[0]->id}")'>Cancel</button></td>
HTML;
        }

        $this->order = $order;
    }
}