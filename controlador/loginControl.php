<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 21/feb/2017
 * Time: 04:27 PM
 */

/**
 * @property ModeloLogin $modelo
 * @property string title enviarCorreo()
 * @property string message enviarCorreo()
 * @property string part_one
 * @property string part_two
 * @property string part_three
 * @property string part_four
 * @property string help
 */
class Login extends Control
{
    private $password;

    protected function cargarPrincipal()
    {
        #Si carga la pantalla de Login cierra la sesión del usuario
        unset($_SESSION[usuario]);
        unset($_SESSION[perfil]);
        unset($_SESSION[conexion]);
    }

    protected function cargarAside()
    {

    }

    function iniciarSesion()
    {
        /**
         * @var $usuario
         * @var $password
         */
        extract($_POST);
        if ($usuario == "" or $password == "") Globales::mensaje_error("Ingrese usuario o contraseña");

        #Encripta la contraseña antes de mandarla al modelo
        $password = Globales::crypt_blowfish_bydinvaders($_POST["password"]);

        #Obtiene el registro del usuario con la funcion en el modelo
        $usuario = $this->modelo->getUsuario($usuario, $password);
        $cambiarPass = (bool)$usuario->idUserCreate;

        #Si el usuario existe llena la variable de usuario en sesión con el id del usuario
        if ($usuario != null) {
            $_SESSION[usuario] = $usuario->idUsuario;
            $_SESSION[perfil] = $usuario->idPerfil;
        } else Globales::mensaje_error("No existe el usuario con los datos ingresados.");

        return compact("cambiarPass");
    }

    function registrarNuevoCliente()
    {
        /**
         * @var $nombre
         * @var $apellidoP
         * @var $apellidoM
         * @var $telefono
         * @var $usuario
         * @var $password
         * @var $repassword
         * @var $reseller
         */
        extract($_POST);
        if ($password != $repassword) Globales::mensaje_error("Las contraseñas no coinciden");
        $dist = false;
        if (empty($password)) {
            $dist = true;
            $password = bin2hex(openssl_random_pseudo_bytes(4));
            $this->password = $password;
        };
        $password = Globales::crypt_blowfish_bydinvaders($password);
        unset($repassword);

        $exTel = explode(")", $telefono);
        $tel = trim($exTel[0], "(");
        #$tel = str_replace("-", "", $exTel[1]);
        $reseller = $reseller == "" ? null : $reseller;

        if ($this->modelo->correoExistente($usuario))
            Globales::mensaje_error("El correo ya esta registrado. Ingrese otro.");

        if (!is_null($reseller)) {
            $idDistribuidor = $this->modelo->distribuidor->selectIdDistribuidorFromToken($reseller);
            if (is_null($idDistribuidor))
                Globales::mensaje_error("Codigo Promocional Invalido. Ingrese Otro.");
        }
        $this->modelo->registrarCliente($nombre, $apellidoP, $apellidoM, "", $tel, $usuario, $token, $idDistribuidor);

        $this->modelo->crearDatabase($token);

        $this->modelo->registrarUsuario($token, "$nombre $apellidoP", $usuario, $password, (int)$dist);

        $this->enviarCorreoBienvenida();

        unset($this->password);
        if ($dist) unset($_SESSION[usuario]);
    }

    /**
     *
     */
    function enviarCorreoBienvenida()
    {
        if ($this->password != "")
            $password = $this->password;
        else {
            $password = "<i>oculta</i>";
        }

        $to = $_POST[usuario];
        $name = $_POST[nombre] . " " . $_POST[apellidoP];
        $subject = "Bienvenida";

        $extra = array("password" => $password);

        $send = new mail("bienvenida", $to, $name, $subject, $extra);
        $send->send_mail($errorInfo);

        if (!$send) {
            Globales::mensaje_error("No Enviado. " . $errorInfo);
        }
    }

    function enviarPassword()
    {
        if (empty($_POST[email]))
            Globales::mensaje_error("El campo de correo no puede estar vacio");

        $usuario = $this->modelo->usuarios->selectUsuarioFromLogin($_POST[email]);
        $password = bin2hex(openssl_random_pseudo_bytes(4));
        $extra = array("password" => $password);

        $password = Globales::crypt_blowfish_bydinvaders($password);
        $this->modelo->editarUsuario($_POST[email], $password);
        $this->modelo->solicitarCambioPass($_POST[email]);

        $send = new mail("password", $_POST[email], $usuario->nombreUsuario, 'Nueva Contraseña', $extra);
        $send->send_mail($errorInfo);

        if (!$send) {
            Globales::mensaje_error("No Enviado. " . $errorInfo);
        }
    }
}