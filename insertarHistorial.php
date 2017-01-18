<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 12/ene/2017
 * Time: 04:25 PM
 */
extract($_POST);
$value1 = "$ask [$mxn | $local]";
request("https://maker.ifttt.com/trigger/bitcoin/with/key/chImOTt-BFhD5zcj3BzzOz", array("value1" => $value1));

date_default_timezone_set('America/Mexico_City');
$date = date("d-m-Y h:i:sa");
$file_data = "$value1 - $date\n";
$file_data .= file_get_contents('historial.txt');
file_put_contents('historial.txt', $file_data);