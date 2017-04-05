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
$objective = isset($_GET['o']) ? $_GET['o'] : 6270 /*6070 /*5900 /*5150 /*4150 /*4100 /*3950 /*3800 /*2700 /*1650 /*2100*/
;
$objectiveBitcoinFix = isset($_GET['b']) ? $_GET['b'] : 0.0307 /*0.01334352 /*0.0137819 /*0.01321236 /*0.01228607 /*0.01011314 /*0.00838254 /*0.01674457 /*0.01410063 /*0.01250791 /*0.01120565 /*0.00974639 /*0.00781269 /*0.00728521 /*0.00612658 /*0.00369227 /*0.10699031*/
;
$objectiveBitcoinFix = number_format(round($objectiveBitcoinFix * 1.02, 8), 8);

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
?>