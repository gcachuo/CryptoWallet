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

    function iniciarSesion()
    {
        /**
         * @var $usuario
         * @var $password
         */
        extract($_POST);
        if ($usuario == "" or $password == "") Globales::mensaje_error("Ingrese usuario o contraseña");

        #Obtiene el registro del usuario con la funcion en el modelo
        $usuario = $this->modelo->usuarios->selectUsuario($usuario);
        if (!password_verify($password, $usuario->passwordUsuario))
            Globales::mensaje_error("No existe el usuario con los datos ingresados.");

        $cambiarPass = (bool)$usuario->idUserCreate;

        #Si el usuario existe llena la variable de usuario en sesión con el id del usuario
        if ($usuario != null) {
            $_SESSION[usuario] = $usuario->idUsuario;
            $_SESSION[perfil] = $usuario->idPerfil;
            $this->modelo->usuarios->updateLastLogin($usuario->idUsuario, date('Y-m-d H:i:s'));
        } else Globales::mensaje_error("No existe el usuario con los datos ingresados.");

        $this->getApiKeys($_POST["password"]);

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

        if ($this->modelo->correoExistente($usuario))
            Globales::mensaje_error("El correo ya esta registrado. Ingrese otro.");

        $this->modelo->registrarCliente($nombre, $apellidoP, $apellidoM, $tel, $usuario);

        $this->modelo->registrarUsuario("$nombre $apellidoP", $usuario, $password);

        unset($this->password);
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

    protected function cargarPrincipal()
    {
        #Si carga la pantalla de Login cierra la sesión del usuario
        unset($_SESSION['usuario']);
        unset($_SESSION['perfil']);
        unset($_SESSION['conexion']);
        unset($_SESSION['api_key']);
        unset($_SESSION['api_secret']);
    }

    protected function cargarAside()
    {

    }
}