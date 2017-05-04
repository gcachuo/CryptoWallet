<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 04:00 PM
 */
class operationsController extends balanceController
{
    public $sellBtc, $sellMxnFee, $priceBtc, $btnBuy;
    public $sellMxn, $sellBtcFee, $priceMxn, $btnSell;

    public $sellEth, $sellEthMxnFee, $priceEth, $btnBuyEth;
    public $sellEthMxn, $sellEthFee, $priceEthMxn, $btnSellEth;

    function __construct()
    {
        parent::__construct();
        $this->sellBtc = number_format(round(($this->btc_mxn - Config::$objectiveBitcoinFix), 8), 8);

        $this->sellMxnFee = round(($this->sellBtc * ($this->ticker->btc_mxn->bid * Config::$minusFee)), 2);

        $this->priceBtc = round($this->ticker->btc_mxn->bid * Config::$minusFee, 2);

        if ($this->sellMxnFee > 0) {
            $this->btnBuy = <<<HTML
<button class="btn btn-default" onclick="buy($this->sellBtc,$this->priceBtc)">Buy</button>
HTML;
        }
        $this->sellMxn = round($this->mxn_btc - Config::$objective, 2);
        $this->sellBtcFee = round($this->sellMxn / ($this->ticker->btc_mxn->ask * Config::$plusFee), 8);
        $this->priceMxn = $this->ticker->btc_mxn->ask * Config::$plusFee;


        $this->sellEth = number_format(round(($this->eth_mxn - Config::$objectiveEthereumFix), 8), 8);

        $this->sellEthMxnFee = round(($this->sellEth * ($this->ticker->eth_mxn->bid * Config::$minusFee)), 2);

        $this->priceEth = round($this->ticker->eth_mxn->bid * Config::$minusFee, 2);

        if ($this->sellEthMxnFee > 0) {
            $this->btnBuyEth = <<<HTML
<button class="btn btn-default" onclick="buy($this->sellEth,$this->priceEth)">Buy</button>
HTML;
        }
        $this->sellEthMxn = round($this->mxn_eth - Config::$objectiveEth, 2);
        $this->sellEthFee = round($this->sellEthMxn / ($this->ticker->eth_mxn->ask * Config::$plusFee), 8);
        $this->priceEthMxn = $this->ticker->eth_mxn->ask * Config::$plusFee;
    }
}