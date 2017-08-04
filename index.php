<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 20/feb/2017
 * Time: 11:21 AM
 */
try {
    ini_set("display_errors", 1);
    ini_set('log_errors', 1);
    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    session_start();
    define(HTTP_PATH_ROOT, "");
    date_default_timezone_set('America/Mexico_City');

    if ($_GET["s"] == "1") session_unset();

    require "globales.php";
    require "control.php";
    require "conexion.php";
    require 'vendor/autoload.php';
    require 'config/bitsoConfig.php';

    Globales::$modulo = $_POST["modulo"] ?: $_SESSION["modulo"];
    Globales::$namespace = __NAMESPACE__ . "\\";
    Globales::setVista();
    Globales::setControl(Globales::$modulo);
} catch (Exception $ex) {
    Globales::mostrar_exception($ex);
}