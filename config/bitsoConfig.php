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
        ## Your account balances
        $balances = $this->bitso->balances();

##sample usage for account balances array
        return $balances->payload->balances;
    }

    function getTrades($book)
    {
        /*
         * book (str):
                Specifies which order book to get user trades from.
         * marker (str, optional):
                Returns objects that are older or newer (depending on 'sort') than the object which
                has the marker value as ID
         * limit (int, optional):
                Limit the number of results to parameter value, max=100, default=25
         * sort (str, optional):
                Sorting by datetime: 'asc', 'desc'
                Default is 'desc'
         */
        $trades = $this->bitso->user_trades(["book" => $book, "limit" => 100]);
        return $trades->payload;
    }
}