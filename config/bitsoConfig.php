<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 04/08/2017
 * Time: 03:30 PM
 */

class bitsoConfig
{
    private $bitso;

    public function __construct()
    {
        $this->bitso = new BitsoAPI\bitso($_SESSION['api_key'], $_SESSION['api_secret']);
    }

    function getTicker($book)
    {
        ## Ticker information
        ## Parameters
        ## [book] - Specifies which book to use
        ##                  - string
        $ticker = $this->bitso->ticker(["book" => $book]);

        ##sample usage for ask price of btc_mxn
        return $ticker->payload;
    }

    function getBalance()
    {
        try {
            ## Your account balances
            $balances = $this->bitso->balances();
        } catch (Exception $ex) {
            $balances = (object)[];
        }

##sample usage for account balances array
        return $balances->payload->balances;
    }

    /**
     * book (str):
     * Specifies which order book to get user trades from.
     * marker (str, optional):
     * Returns objects that are older or newer (depending on 'sort') than the object which
     * has the marker value as ID
     * limit (int, optional):
     * Limit the number of results to parameter value, max=100, default=25
     * sort (str, optional):
     * Sorting by datetime: 'asc', 'desc'
     * Default is 'desc'
     */
    function getTrades($book)
    {
        $trades = $this->bitso->user_trades(["book" => $book, "limit" => 100]);
        return $trades->payload;
    }

    /**
     * Places a buy limit order.
     * Args:
     * book (str):
     * Specifies which book to use.
     * side (str):
     * the order side (buy or sell)
     * order_type (str):
     * Order type (limit or market)
     * major (str):
     * The amount of major currency for this order. An order could be specified in terms of major or minor, never both.
     * minor (str):
     * The amount of minor currency for this order. An order could be specified in terms of major or minor, never both.
     * price (str):
     * Price per unit of major. For use only with limit orders.
     * return A bitso.Order instance.
     */
    public function crearOrden($book, $monto, $precio, $side)
    {
        if ($side == "sell") {
            $monto = round($monto * 0.98, 2);
            $precio -= 0.01;
        } else {
            $monto = round($monto * 1.02, 2);
        }
        $monto = round($monto / $precio, 6);
        $args = array(
            "book" => $book,
            "side" => $side,
            "type" => "limit",
            "major" => $monto,
            "price" => $precio
        );
        $this->bitso->place_order($args);
    }

    /**
     * Get a list of the user's open orders
     * Args:
     * book (str):
     * Specifies which book to use. Default is btc_mxn
     *
     * Returns:
     * A list of bitso.Order instances.
     */
    public function getActive($tipo, $book)
    {
        try {
            $buy = [];
            $sell = [];
            $args = [
                "book" => $book
            ];
            $open = $this->bitso->open_orders($args);
            foreach ($open->payload as $order) {
                array_push(${$order->side}, $order);
            }
            $active = !empty($$tipo);
        } catch (Exception $ex) {
            $active = true;
        }
        return $active;
    }

    function getOrders($book)
    {
        $orders = [];
        $args = [
            "book" => $book
        ];
        $open = $this->bitso->open_orders($args);
        foreach ($open->payload as $order) {
            array_push($orders, $order);
        }
        return $orders;
    }

    public function deleteOrder($id)
    {
        $this->bitso->cancel_order(["order_id" => $id]);
    }
}