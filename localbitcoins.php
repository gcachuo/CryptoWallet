<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 13/ene/2017
 * Time: 12:07 PM¿¿
 */

$ticker = request("http://localbitcoins.com/bitcoincharts/mxn/orderbook.json");

$localbitcoins = $ticker->bids[0][0];

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