<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/abr/2017
 * Time: 06:03 PM
 */
define(ROOT, "../../");
echo $_GET[correo]();

function bienvenida()
{
    require ROOT . "control.php";
    require ROOT . "controlador/loginControl.php";
    $login = new Login();

    $to = "usuario";
    $password = "password";
    $name = "Nombre Apellido";

    $title = "Hola $name";
    $message = <<<HTML
<p>Bienvenido a Cbiz Money, la herramienta donde podrás registrar tus gastos y tenerlos preparados para tu agente de
    TAX</p>

<p>Cbiz Money esta siempre disponible para tu uso desde cualquier teléfono, Tablet o computadora con internet solo
    entra a:</p>
<a href="http://www.bd.mx/money/">http://www.bd.mx/money/</a>

<div>Tus datos de acceso son:</div>
<div>Usuario: $to</div>
<div>Password: $password</div>
HTML;

    $data = array(
        "title" => $title,
        "message" => $message,
        "part_one" => $part_one,
        "part_two" => $part_two,
        "part_three" => $part_three,
        "part_four" => $part_four,
        "part_five" => $part_five,
    );

    return $login->cargarBodyCorreo('bienvenida', $data);
}