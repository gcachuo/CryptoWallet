<?php


namespace Helper;


use BitsoAPI\bitsoException;
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

        $this->api_key = System::decrypt($keys['api_key']);
        $this->api_secret = System::decrypt($keys['api_secret']);
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
            JsonResponse::sendResponse(['message' => $exception->getMessage(), 'error' => $exception], HTTPStatusCodes::ServiceUnavailable);
        }
    }

    public function cancelOrder($oid)
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        $bitso->cancel_order(['order_id' => $oid]);
    }
}

abstract class BitsoOrderPayload
{
    public $original_value;
    public $unfilled_amount;
    public $original_amount;
    public $book;
    public $created_at;
    public $updated_at;
    public $side;
    public $type;
    public $oid;
    public $status;
    public $price;
    public $time_in_force;
}

/*abstract class BitsoOrder
{
    public bool $success;
    public BitsoOrderPayload $payload;
}*/
