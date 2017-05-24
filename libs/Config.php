<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 12:20 PM
 */
class Config
{
    static public
        $objective, $objectiveEth,
        $objectiveBitcoinFix,
        $objectiveEthereumFix,
        $key = "SUqaCnPIQu",
        $plusFee,
        $minusFee,
        $plusWithdraw = 0;
    static private
        $bitsoKey = "131376",
        $bitsoSecret = "48792544eec665a1f3f5cd84ec2c7fcb";

    static function request($url, $data = array())
    {
        try {
            set_time_limit(60);
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
                $result = new stdClass();
                $result->error->message = "Error al conectar";
            } else
                $result = json_decode($result);
            if (isset($result->error))
                exit($result->error->message);
        } catch (Exception $ex) {
            exit($ex->getMessage());
        }
        return $result;
    }

    static function generateSignature(&$nonce, &$signature)
    {
        $nonce = round(microtime(true) * 1000) * 2;
        $message = $nonce . Config::$bitsoKey . Config::$key;
        $signature = hash_hmac('sha256', $message, Config::$bitsoSecret);
    }
}