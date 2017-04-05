<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 01:03 PM
 */
require_once "tickerController.php";

class balanceController extends tickerController
{
    public $balance,
        $btc_eth,
        $btc_mxn,
        $eth_mxn,
        $mxn_btc,
        $mxn_eth,
        $eth_btc;

    function __construct()
    {
        parent::__construct();
        $this->setBalance();

        Config::$plusFee = 1 + ($this->balance->fee / 100);
        Config::$minusFee = 1 - ($this->balance->fee / 100);

        $this->btc_mxn = round(($this->balance->mxn_balance + Config::$plusWithdraw) / ($this->ticker->btc_mxn->ask * Config::$minusFee), 8);

        $this->btc_eth = round(($this->balance->mxn_balance * ($this->ticker->eth_mxn->ask * Config::$minusFee)) / ($this->balance->mxn_balance * $this->ticker->btc_mxn->ask * Config::$minusFee), 8);

        $this->mxn_btc = round($this->balance->btc_balance * ($this->ticker->btc_mxn->ask * Config::$plusFee), 2);

        $this->mxn_eth = round($this->balance->eth_balance * ($this->ticker->eth_mxn->ask * Config::$plusFee), 2);

        $this->eth_mxn = round(($this->balance->mxn_balance + Config::$plusWithdraw) / ($this->ticker->eth_mxn->ask * Config::$minusFee), 8);

        $this->eth_btc = round(($this->balance->mxn_balance * ($this->ticker->btc_mxn->ask * Config::$minusFee)) / ($this->balance->mxn_balance * $this->ticker->eth_mxn->ask * Config::$minusFee), 8);
    }

    function setBalance()
    {
        require_once "libs/Config.php";
        Config::generateSignature($nonce, $signature);
        $keys = array("key" => Config::$key, "nonce" => $nonce, "signature" => $signature);
        $balance = Config::request("https://api.bitso.com/v2/balance/", $keys);
        $this->balance = $balance;
    }
}