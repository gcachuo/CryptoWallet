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
 */
class ModeloLogin extends Tabla
{
    function getUsuario($login, $password)
    {
        return $this->usuarios->selectUsuario($login, $password);
    }

    function registrarCliente($nombre, $apellidoP, $apellidoM, $lada, $telefono, $correo, &$tokenCbiz, $idDistribuidor = null)
    {
        Globales::setNamespace("distribuidor");

        $idCliente = $this->cliente->insertCliente("$nombre $apellidoP $apellidoM", $lada, $telefono, date('Y-m-d'), 1, $idDistribuidor ?: 1);

        $this->contacto_cliente->insertContactoCliente($idCliente, $nombre, $apellidoP, $apellidoM, $lada . $telefono, $correo);

        $estatus = false;
        $tokenCbiz = null;
        $num = 1;
        while (!$estatus) {
            $tokenCbiz = $this->generarToken($nombre, $apellidoP, $apellidoM, $num, $estatus);
            $num++;
        }

        $this->cbiz_cliente->insertarCbizCliente($idCliente, $tokenCbiz, date('Y-m-d'), date('Y-m-d'), 0, date('Y-m-d'), date('Y-m-d'), 1);

    }

    function registrarUsuario($token, $nombre, $email, $password, $reseller)
    {
        Globales::setNamespace("");
        Tabla::setToken($token);
        $_SESSION[usuario] = $this->usuarios->insertUsuario($nombre, $email, $password, $email, 0, $reseller);
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
        Globales::setNamespace("distribuidor");
        $existe = boolval($this->contacto_cliente->selectCountCorreo($correo));
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