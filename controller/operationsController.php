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
    }
}