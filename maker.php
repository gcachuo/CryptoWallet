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

request("https://maker.ifttt.com/trigger/bitcoin/with/key/chImOTt-BFhD5zcj3BzzOz", array("value1" => "$mxn | $localbitcoins"));

$file_data = "$mxn\n";
$file_data .= file_get_contents('historial.txt');
file_put_contents('historial.txt', $file_data);

echo $mxn;

function request($url, $data = array())
{
// use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { /* Handle error */
    }
    return json_decode($result);
}