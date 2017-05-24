<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 12:12 PM
 */
register_shutdown_function('shutdown');
error_reporting(E_ALL ^ (E_WARNING | E_NOTICE));
require 'libs/FrontController.php';
FrontController::main();

function shutdown()
{
    $a = error_get_last();
    if ($a == null)
        echo "No errors";
    else
        print_r($a);

}