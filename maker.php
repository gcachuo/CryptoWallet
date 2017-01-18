<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 12/ene/2017
 * Time: 05:30 PM
 */
include "keys.php";
include "localbitcoins.php";

$ticker = request("https://api.bitso.com/v2/ticker");
$balance = request("https://api.bitso.com/v2/balance", array("key" => $key, "nonce" => $nonce, "signature" => $signature));

$ask = $ticker->ask * 0.99;
$mxn = round($balance->btc_balance * $ask, 2) + $balance->mxn_balance;
$local = round($localask * $balance->btc_balance, 2) + $balance->mxn_balance;

include "insertarHistorial.php";

echo $mxn . " | " . $local;