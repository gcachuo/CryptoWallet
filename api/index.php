<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 14/11/18
 * Time: 06:40 PM
 */

$version = '181114';
define('SEED', 'crypt0w4113t');

require "libs/system.php";
require "libs/database.php";
require "vendor/autoload.php";

setcookie('XDEBUG_SESSION', 'PHPSTORM');
error_reporting(E_ALL);
ini_set('display_errors', 1);
spl_autoload_register('auto_loader');
set_error_handler('error_handler');
register_shutdown_function('shutdown_function');

header('Content-type: application/json');

try {

    $controller = isset_get($_REQUEST['controller']);
    $action = isset_get($_REQUEST['action']);

    /** @var array|string|null $response */
    if (empty($controller) || empty($action)) {
        $response = null;
    } else {
        $controller = new $controller();
        $response = method_exists($controller, $action) ? $controller->$action() : null;
    }
} catch (Exception $exception) {
    $errno = $exception->getCode() ?: 400;
    $message = $exception->getMessage();
    $errfile = $exception->getFile();
    $errline = $exception->getLine();
    http_response_code($errno);

    die(json_encode(['response' => 'App Error', 'code' => http_response_code(), 'error' => compact('errno', 'message', 'errfile', 'errline')]));
}

echo json_encode(['response' => $response, 'code' => http_response_code(), 'version' => $version]);