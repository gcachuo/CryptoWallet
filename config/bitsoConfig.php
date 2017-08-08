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
}