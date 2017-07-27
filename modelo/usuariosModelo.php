<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 23/feb/2017
 * Time: 06:21 PM
 */

/**
 * @property TablaUsuarios usuarios
 * @property TablaPerfiles perfiles
 */
class ModeloUsuarios extends Tabla
{
    public function getRegistrosUsuarios()
    {
        return $this->usuarios->selectRegistrosUsuarios();
    }

    function registrarUsuario($nombre, $login, $password, $correo, $perfil)
    {
        $usuario = $this->usuarios->selectUsuarioFromLogin($login);

        if ($usuario->estatus == true)
            mensaje_error("El usuario ya existe");
        else
            $this->usuarios->insertUsuario($nombre, $login, $password, $correo, $perfil);
    }

    function obtenerUsuario($id)
    {
        return $this->usuarios->selectUsuarioFromId($id);
    }

    function editarUsuario($id, $nombre, $usuario, $correo, $perfil, $password)
    {
        $this->usuarios->updateUsuario($id, $nombre, $usuario, $correo, $perfil, $password);
    }

    function eliminarUsuario($id)
    {
        $this->usuarios->updateEstatusUsuario($id);
    }

    function obtenerPerfiles()
    {
        return $this->perfiles->selectPerfiles();
    }
}