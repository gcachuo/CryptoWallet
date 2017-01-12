<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 11/ene/2017
 * Time: 05:48 PM
 */

$key = "SUqaCnPIQu";
$nonce = round(microtime(true) * 1000);
$bitsoClientId = "131376";
$message = $nonce . $bitsoClientId . $key;
$secret = "48792544eec665a1f3f5cd84ec2c7fcb";
$signature = hash_hmac('sha256', $message, $secret);
$objective = 1100;