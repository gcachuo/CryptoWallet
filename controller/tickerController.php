<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 01:25 PM
 */
class tickerController
{
    public $ticker;

    function __construct()
    {
        $this->setTicker();
    }

    function setTicker()
    {
        require_once "libs/Config.php";
        $this->ticker->btc_mxn = Config::request("https://api.bitso.com/v2/ticker/?book=btc_mxn");

        $this->ticker->eth_mxn = Config::request("https://api.bitso.com/v2/ticker/?book=eth_mxn");
    }
}