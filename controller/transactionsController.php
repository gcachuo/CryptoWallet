<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 05:17 PM
 */
class transactionsController
{
    public $transactions;

    function __construct()
    {
        $this->setTransactions();
    }

    function setTransactions()
    {
        $key = Config::$key;
        Config::generateSignature($nonce, $signature);
        $keys = array("key" => $key, "nonce" => $nonce, "signature" => $signature);
        $trades = Config::request("https://api.bitso.com/v2/user_transactions/", $keys);
        $objectiveBitcoin = 0;
        foreach ($trades as $trade) {
            $firstDate = date_create_from_format('Y-m-d', "2017-02-01");
            $date = date_create_from_format('Y-m-d H:i:s', $trade->datetime);

            $btc_mxn = $trade->btc < 0 ? $trade->btc_mxn * 0.99 : $trade->btc_mxn * 1.01;
            if ($btc_mxn == 0) continue;

            if ($date > $firstDate and $trade->btc < 0) {
                switch ($trade->id) {
                    case 300933:
                        continue;
                    default:
                        $objectiveBitcoin += $trade->btc;
                        break;
                }
            }

            $printdate = $date->format('d/m/Y');
            $roundmxn = round($trade->mxn, 2);
            $tradeBtc = number_format(round($trade->btc, 8), 8);
            $this->transactions .= <<<HTML
<tr>
    <td>$printdate</td>
    <td>$tradeBtc</td>
    <td>$roundmxn</td>
    <td>$btc_mxn</td>
</tr>
HTML;
        }
    }
}