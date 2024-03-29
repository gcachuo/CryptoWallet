<?php


namespace Helper;


use BitsoAPI\bitsoException;
use CoreException;
use HTTPStatusCodes;
use Model\Usuarios_Keys;
use System;

class Bitso extends \BitsoAPI\bitso
{
    /**
     * @var false|string
     */
    private string $api_key;
    /**
     * @var false|string
     */
    private string $api_secret;

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
    function lookupOrder(string $oid): BitsoOrderPayload
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
        return System::objectToObject($bitso->lookup_order([$oid])->payload[0], get_class(new BitsoOrderPayload()));
    }

    /**
     * @param string $oid
     * @return BitsoTradePayload
     */
    function orderTrades(string $oid): BitsoTradePayload
    {
        $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);

        $order_trade = [
            'book' => '',
            'created_at' => '',
            'minor' => '',
            'major' => '',
            'fees_amount' => '',
            'fees_currency' => '',
            'minor_currency' => '',
            'major_currency' => '',
            'oid' => '',
            'tid' => '',
            'price' => '',
            'side' => '',
            'maker_side' => ''
        ];
        foreach ($bitso->order_trades($oid)->payload as $trade) {
            /** @var BitsoTradePayload $trade */
            $trade = System::objectToObject($trade, get_class(new BitsoTradePayload()));
            if (empty($order_trade['book'])) {
                $order_trade = (array)$trade;
            } else {
                $order_trade['minor'] += $trade->minor;
                $order_trade['major'] += $trade->major;
                $order_trade['fees_amount'] += $trade->fees_amount;
                $order_trade['price'] = round($order_trade['minor'] / abs($order_trade['major']), 2);
            }
        }

        return new BitsoTradePayload($order_trade);
    }

    function selectBalances(): array
    {
        try {
            $bitso = new \BitsoAPI\bitso($this->api_key, $this->api_secret);
            return $bitso->balances()->payload->balances;
        } catch (bitsoException $exception) {
            return [];
        }
    }

    /**
     * @return bool
     */
    public function isKeySet(): bool
    {
        return !empty($this->api_key) && !empty($this->api_secret);
    }
}
