<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 11/ene/2017
 * Time: 05:48 PM
 */

$HTTPMethod = "GET";
$RequestPath = "/v3/balance/";
$JSONPayload = "";
$key = "SUqaCnPIQu";
$bitsoKey = "131376";
$bitsoSecret = "48792544eec665a1f3f5cd84ec2c7fcb";
$objective = isset($_GET['o']) ? $_GET['o'] : 2700 /*1650 /*2100*/
;
$objectiveBitcoin = isset($_GET['b']) ? $_GET['b'] : 0.00781269 /*0.00728521 /*0.00612658 /*0.00369227 /*0.10699031*/
;

// Create signature
/*$message = $nonce . $HTTPMethod . $RequestPath . $JSONPayload;
$signature = hash_hmac('sha256', $message, $bitsoSecret);*/

// Build the auth header
$format = 'Bitso %s:%s:%s';
$authHeader = sprintf($format, $bitsoKey, $nonce, $signature);

function generateSignature($key, $bitsoKey, $bitsoSecret, &$nonce, &$signature)
{
    $nonce = round(microtime(true) * 1000);
    $message = $nonce . $bitsoKey . $key;
    $signature = hash_hmac('sha256', $message, $bitsoSecret);
}

function getTrades($arrayTrades)
{
    $stringTrades = "";
    return $stringTrades;
}