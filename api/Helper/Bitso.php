<?php


namespace Helper;


use BitsoAPI\bitsoException;
use CoreException;
use HTTPStatusCodes;
use JsonResponse;
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

        if ($keys) {
            $api_key = System::decrypt($keys['api_key']);
            $api_secret = System::decrypt($keys['api_secret']);

            System::check_value_empty(compact('api_key', 'api_secret'), ['api_key', 'api_secret'], 'Decryption failed, check seed');

            $this->api_key = $api_key;
            $this->api_secret = $api_secret;
        }
    }

    /**
     * @param $id_moneda
     * @param $costo
     * @return array
     */
    public function placeOrder($id_moneda, $costo)
    {
        try {
            $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
            $place_order = $bitso->place_order(['book' => "{$id_moneda}_mxn", 'side' => 'sell', 'type' => 'market', 'minor' => $costo]);
            sleep(10);
            /** @var BitsoOrder $orders */
            $orders = $bitso->lookup_order([$place_order->payload->oid]);
            return compact('place_order', 'orders');
        } catch (bitsoException $exception) {
            throw new CoreException($exception->getMessage(), HTTPStatusCodes::ServiceUnavailable, compact('exception'));
        }
    }

    public function cancelOrder($oid)
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        $bitso->cancel_order(['order_id' => $oid]);
    }

    public function speiWithdrawal(float $amount, string $first_name, string $last_name, string $CLABE)
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        $bitso->spei_withdrawal([
            'amount' => $amount,
            'recipient_given_names' => $first_name,
            'recipient_family_names' => $last_name,
            'clabe' => $CLABE,
        ]);
    }

    /**
     * @param string $oid
     * @return mixed
     */
    function lookupOrder(string $oid)
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        return $bitso->lookup_order([$oid])->payload;
    }

    /**
     * @param string $oid
     * @return mixed
     */
    function orderTrades(string $oid)
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        return $bitso->order_trades($oid)->payload;
    }
}
