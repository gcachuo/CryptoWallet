<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 08/08/2017
 * Time: 12:26 PM
 */

/**
 * @property ModeloConfig modelo
 */
class Config extends Control
{

    protected function cargarPrincipal()
    {
        // TODO: Implement cargarPrincipal() method.
    }

    protected function cargarAside()
    {
        // TODO: Implement cargarAside() method.
    }

    function cambiarLlavesApi()
    {
        $password = Globales::crypt_blowfish_bydinvaders($_POST["pass"]);
        $login = $this->modelo->usuarios->selectUsuarioFromId($_SESSION["usuario"])->login;
        $usuario = $this->modelo->usuarios->selectUsuario($login, $password);
        if (is_null($usuario)) {
            Globales::mensaje_error("Contraseña Incorrecta");
        } else {
            $apiKey = Globales::encrypt($_POST['api_key'], $_POST["pass"]);
            $apiSecret = Globales::encrypt($_POST['api_secret'], $_POST["pass"]);
            $this->modelo->usuario_llaves->insertApiKeys($apiKey, $apiSecret, $_SESSION["usuario"]);
        }
    }
}