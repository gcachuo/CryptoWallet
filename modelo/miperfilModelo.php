<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/abr/2017
 * Time: 05:03 PM
 */

/**
 * @property TablaUsuarios usuarios
 * @property distribuidor\TablaCliente cliente
 * @property distribuidor\TablaContacto_Cliente contacto_cliente
 * @property distribuidor\TablaDistribuidor distribuidor
 */
class ModeloMiPerfil extends Tabla
{
    function obtenerCorreo($idUsuario)
    {
        $usuario = $this->usuarios->selectUsuarioFromId($idUsuario);
        return $usuario->correo;
    }

    function obtenerAgente($idUsuario)
    {
        $correo = $this->obtenerCorreo($idUsuario);
        Globales::setNamespace("distribuidor");
        $idCliente = $this->contacto_cliente->selectIdClienteFromCorreo($correo);
        $idDistribuidor = $this->cliente->selectIdDistribuidorFromId($idCliente);
        $agente = $this->distribuidor->selectDistribuidorFromId($idDistribuidor);
        Globales::setNamespace("");
        return $agente;
    }

    function editarUsuario($idUsuario, $email, $password)
    {
        $usuario = $this->usuarios->selectUsuarioFromId($idUsuario);

        $this->usuarios->updateUsuario($idUsuario, $usuario->nombre, $email, $email, $usuario->perfil, $password);
    }
}