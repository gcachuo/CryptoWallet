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
        $eth_btc,
        $objectiveBid,
        $objectiveAsk,
        $totalBtc,
        $totalEth;

    function __construct()
    {
        parent::__construct();
        $this->setBalance();

        $jsonObjective = json_decode(file_get_contents("config/objective.json"));

        Config::$objective = round(isset($_GET['o']) ? $_GET['o'] : $jsonObjective->mxn * 1.02, 2);
        Config::$objectiveEth = round(isset($_GET['o']) ? $_GET['o'] : $jsonObjective->ethmxn * 1.02, 2);
        Config::$objectiveBitcoinFix = (isset($_GET['b']) ? $_GET['b'] : $jsonObjective->btc) * 1.02;
        Config::$objectiveEthereumFix = (isset($_GET['e']) ? $_GET['e'] : $jsonObjective->eth) * 1.02;
        Config::$plusFee = 1 + ($this->balance->fee / 100);
        Config::$minusFee = 1 - ($this->balance->fee / 100);

        $this->btc_mxn = round(($this->balance->mxn_balance + Config::$plusWithdraw) / ($this->ticker->btc_mxn->ask * Config::$minusFee), 8);

        $this->btc_eth = round($this->balance->eth_balance * ($this->ticker->eth_mxn->ask * Config::$plusFee) / ($this->ticker->btc_mxn->ask * Config::$plusFee), 8);

        $this->mxn_btc = round($this->balance->btc_balance * ($this->ticker->btc_mxn->ask * Config::$plusFee), 2);

        $this->mxn_eth = round($this->balance->eth_balance * ($this->ticker->eth_mxn->ask * Config::$plusFee), 2);

        $this->eth_mxn = round(($this->balance->mxn_balance + Config::$plusWithdraw) / ($this->ticker->eth_mxn->ask * Config::$minusFee), 8);

        $this->eth_btc = ($this->balance->btc_balance * ($this->ticker->btc_mxn->ask * Config::$plusFee)) / ($this->ticker->eth_mxn->ask * Config::$plusFee);

        $this->objectiveBid = round((Config::$objective / $this->balance->btc_balance) * Config::$minusFee, 2);
        $this->objectiveAsk = round((($this->balance->mxn_balance + Config::$plusWithdraw) / (Config::$objectiveBitcoinFix * Config::$plusFee)), 2);

        $this->totalBtc = round($this->mxn_btc + $this->balance->mxn_balance, 2);
        $this->totalEth = round($this->mxn_eth + $this->balance->mxn_balance, 2);
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