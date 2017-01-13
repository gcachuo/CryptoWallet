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

$bid = $ticker->bid - ($ticker->bid * 0.01);
$mxn = round($balance->btc_available * $bid, 2);
$local = round($localbitcoins * $balance->btc_available, 2);

request("https://maker.ifttt.com/trigger/bitcoin/with/key/chImOTt-BFhD5zcj3BzzOz", array("value1" => "$mxn | $local"));

date_default_timezone_set('America/Mexico_City');
$date = date("d-m-Y h:i:sa");
$file_data = "$mxn | $local - $date\n";
$file_data .= file_get_contents('historial.txt');
file_put_contents('historial.txt', $file_data);

echo $mxn . " | " . $local;