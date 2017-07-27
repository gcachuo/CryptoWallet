<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/abr/2017
 * Time: 04:34 PM
 */

/**
 * @property ModeloMiPerfil modelo
 */
class MiPerfil extends Control
{
    public $correo, $agente;

    protected function cargarPrincipal()
    {
        // TODO: Implement cargarPrincipal() method.
    }

    protected function cargarAside()
    {
        switch ($_POST[asideAccion]) {
            case "miperfil":
                $this->correo = $this->modelo->obtenerCorreo($_SESSION[usuario]);
                break;
            case "cuenta":
                $this->agente = $this->modelo->obtenerAgente($_SESSION[usuario]);
                break;
        }
    }

    function guardarCambios()
    {
        if ($_POST[password] != $_POST[repassword])
            Globales::mensaje_error("Las contraseñas no coinciden");

        $password = Globales::crypt_blowfish_bydinvaders($_POST[password]);

        $this->modelo->editarUsuario($_SESSION[usuario], $_POST[email], $password);
    }
}