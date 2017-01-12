<?php
/**
 * Created by PhpStorm.
 * User: Memo
 * Date: 12/ene/2017
 * Time: 04:25 PM
 */

$file_data = "$_POST[cash]\n";
$file_data .= file_get_contents('historial.txt');
file_put_contents('historial.txt', $file_data);