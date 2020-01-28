<?php


namespace Helper;


use Model\Usuarios_Keys;
use System;

class Bitso
{
    /**
     * @var false|string
     */
    private $api_key;
    /**
     * @var false|string
     */
    private $api_secret;

    public function __construct($user_id)
    {
        $Usuarios_Keys = new Usuarios_Keys();
        $keys = $Usuarios_Keys->selectKeys($user_id);

        $this->api_key = System::decrypt($keys['api_key']);
        $this->api_secret = System::decrypt($keys['api_secret']);
    }


    public function placeOrder($id_moneda, $costo)
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        $place_order = $bitso->place_order(['book' => "{$id_moneda}_mxn", 'side' => 'sell', 'type' => 'market', 'minor' => $costo]);
        sleep(10);
        $orders = $bitso->lookup_order([$place_order->payload->oid]);
        return compact('place_order', 'orders');
    }

    public function cancelOrder($oid)
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        $bitso->cancel_order(['order_id' => $oid]);
    }
}
