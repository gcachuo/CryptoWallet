<?php
/**
 * Created by PhpStorm.
 * User: memo
 * Date: 15/11/18
 * Time: 05:16 PM
 */

/**
 * @param $variable
 * @param null $return
 * @return null
 */
function isset_get(&$variable, $return = null)
{
    if (isset($variable) and !empty($variable)) {
        return $variable;
    }
    return $return;
}

/**
 * @param string $error
 * @param int $code
 * @throws Exception
 */
function set_error($error, $code = 0)
{
    throw new Exception($error, $code);
}

/**
 * @param int $errno
 * @param string $message
 * @param string $errfile
 * @param int $errline
 */
function error_handler($errno, $message, $errfile, $errline)
{
    http_response_code(500);
    die(json_encode(['response' => 'System Error', 'code' => http_response_code(), 'error' => compact('errno', 'message', 'errfile', 'errline')]));
}

function shutdown_function()
{
    $error = error_get_last();
    if ($error !== NULL) {
        ob_clean();
        http_response_code(500);
        die(json_encode(['response' => 'Fatal Error', 'code' => http_response_code(), 'error' => $error]));
    }
}

function auto_loader($clase)
{
    include 'controllers/' . $clase . '.php';
}

function encrypt($value)
{
    $value_encrypted = openssl_encrypt($value, "AES-256-CBC", SEED, 0, str_pad(SEED, 16, 'X', STR_PAD_LEFT));
    return $value_encrypted;
}

function decrypt($value_encrypted)
{
    $value = openssl_decrypt($value_encrypted, "AES-256-CBC", SEED, 0, str_pad(SEED, 16, 'X', STR_PAD_LEFT));
    return $value;
}