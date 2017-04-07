<?php
/**
 * Created by PhpStorm.
 * User: gcach
 * Date: 06/abr/2017
 * Time: 09:13 PM
 */
error_reporting(E_ERROR);
try {
    $json = json_encode($_REQUEST[ajax]());
    echo $json;
}
catch (Exception $ex){
    echo json_encode(array("error"=>$ex->getMessage()));
}

function generateSignature(){
    require_once "Config.php";
    Config::generateSignature($nonce,$signature);
    return array("key"=>Config::$key,"nonce"=>$nonce,"signature"=>$signature);
}