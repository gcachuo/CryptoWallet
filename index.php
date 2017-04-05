<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 12:12 PM
 */
error_reporting(E_ALL ^ (E_WARNING | E_NOTICE));
require 'libs/FrontController.php';
FrontController::main();