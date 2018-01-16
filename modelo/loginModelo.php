<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 23/feb/2017
 * Time: 10:45 AM
 */

/**
 * Class ModeloLogin
 * @property TablaUsuarios usuarios
 * @property TablaContacto_Clientes contacto_clientes
 * @property TablaClientes clientes
 * @property TablaUsuario_Llaves usuario_llaves
 */
class ModeloLogin extends Modelo
{
    function getUsuario($login, $password)
    {
        return $this->usuarios->selectUsuario($login, $password);
    }

    function registrarCliente($nombre, $apellidoP, $apellidoM, $telefono, $correo)
    {
        $idCliente = $this->clientes->insertCliente("$nombre $apellidoP $apellidoM");

        $this->contacto_clientes->insertContactoCliente($idCliente,  $telefono, $correo);
    }

    function registrarUsuario($nombre, $email, $password)
    {
        $_SESSION['usuario'] = $this->usuarios->insertUsuario($nombre, $email, $password, $email, 1);
    }

    function crearDatabase($token)
    {
        $ruta = HTTP_PATH_ROOT . "modelo/database/e11_cbizcontrol.php";
        include_once $ruta;
        $db = new distribuidor\DbCbizControl();
        $db->createNewDatabase($token);
    }

    function generarToken($nombre, $apellidoP, $apellidoM, $num, &$estatus)
    {
        $token = mb_strtoupper(substr($nombre, 0, 1) . substr($apellidoP, 0, 1) . substr($apellidoM, 0, 1)) . str_pad($num, 3, '0', STR_PAD_LEFT);
        $estatus = !boolval($this->cbiz_cliente->selectTokenExistente($token));
        return $token;
    }

    function correoExistente($correo)
    {
        $existe = boolval($this->contacto_clientes->selectCountCorreo($correo));
        return $existe;
    }

    function editarUsuario($email, $password)
    {
        $usuario = $this->usuarios->selectUsuarioFromLogin($email);

        $this->usuarios->updateUsuario($usuario->id, $usuario->nombre, $usuario->email, $usuario->email, $usuario->perfil, $password);
    }

    function solicitarCambioPass($email)
    {
        $usuario = $this->usuarios->selectUsuarioFromLogin($email);

        $this->usuarios->updateIdUserCreate($usuario->id, 1);
    }
}