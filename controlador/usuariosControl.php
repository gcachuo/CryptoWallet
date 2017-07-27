<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 23/feb/2017
 * Time: 02:17 PM
 */

/**
 * @property ModeloUsuarios modelo
 */
class Usuarios extends Control
{
    protected $tablaUsuarios, $listaPerfiles;

    protected function cargarPrincipal()
    {
        $this->permisos = Globales::getPermisos($_SESSION["modulo"]);
        $this->generarTablaUsuarios();
    }

    protected function cargarAside()
    {
        $this->generarListaPerfiles();
    }

    /**
     * @return string
     */
    function generarTablaUsuarios()
    {
        $tablaUsuarios = "";
        $usuarios = $this->modelo->getRegistrosUsuarios();

        foreach ($usuarios as $usuario) {
            /**
             * @var $id
             * @var $nombre
             * @var $login
             * @var $perfil
             */
            extract($usuario);
            if ($this->permisos->editar)
                $btnEditar = <<<HTML
<a title="{$this->idioma->btnEditar}" class="btn btn-sm btn-default" onclick="navegar('usuarios', 'nuevo', {idUsuario: $id, modo: 'vistaEditarUsuario'});">
    <i class="material-icons">edit</i>
</a>
HTML;
            if ($this->permisos->eliminar)
                $btnEliminar = <<<HTML
<a title="{$this->idioma->btnEliminar}" class="btn btn-sm btn-default" onclick="btnEliminarUsuario($id);">
    <i class="material-icons">delete</i>
</a>
HTML;


            $acciones = $btnEditar . $btnEliminar;
            $tablaUsuarios .= <<<HTML
<tr>
    <td>$nombre</td>
    <td>$login</td>
    <td>$perfil</td>
    <td class="tdAcciones">$acciones</td>
</tr>
HTML;
        }
        $this->tablaUsuarios = $tablaUsuarios;
        return $tablaUsuarios;
    }

    function generarListaPerfiles()
    {
        $perfiles = $this->modelo->obtenerPerfiles();
        foreach ($perfiles as $perfil) {
            $this->listaPerfiles .= <<<HTML
<option value="$perfil[idPerfil]">$perfil[nombrePerfil]</option>
HTML;
        }
    }

    function vistaEditarUsuario()
    {
        return $this->modelo->obtenerUsuario($_POST["idUsuario"]);
    }

    function registrarUsuario()
    {
        /**
         * @var $nombre
         * @var $usuario
         * @var $password
         * @var $repass
         * @var $email
         * @var $perfil
         */
        extract($_POST);
        if ($nombre == "") Globales::mensaje_error("Ingrese un nombre");
        if ($usuario == "") Globales::mensaje_error("Ingrese un usuario");
        if ($password == "") Globales::mensaje_error("Ingrese una contraseña");
        if ($repass != $password) Globales::mensaje_error("Las contraseñas no coinciden");

        $password = Globales::crypt_blowfish_bydinvaders($password);
        $this->modelo->registrarUsuario($nombre, $usuario, $password, $email, $perfil);
    }

    function editarUsuario()
    {
        /**
         * @var $idUsuario
         * @var $nombre
         * @var $usuario
         * @var $email
         * @var $perfil
         * @var $password
         * @var $repass
         */
        extract($_POST);
        if ($nombre == "") Globales::mensaje_error("Ingrese un nombre");
        if ($usuario == "") Globales::mensaje_error("Ingrese un usuario");
        if ($repass != $password) Globales::mensaje_error("Las contraseñas no coinciden");
        if ($password != "") $password = Globales::crypt_blowfish_bydinvaders($password);

        $this->modelo->editarUsuario($idUsuario, $nombre, $usuario, $email, $perfil, $password);
    }

    function eliminarUsuario()
    {
        $this->modelo->eliminarUsuario($_POST["idUsuario"]);
    }
}