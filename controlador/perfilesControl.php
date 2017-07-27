<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/feb/2017
 * Time: 05:36 PM
 */

/**
 * @property ModeloPerfiles modelo
 */
class Perfiles extends Control
{
    protected $tablaPerfiles;
    protected $arbolPermisos;
    protected $modeloModulos;
    protected $modeloAcciones;
    private $permisosPerfiles;

    protected function cargarPrincipal()
    {

        #$this->permisosModulo(1002);
        $this->generarTablaPerfiles();
    }

    protected function cargarAside()
    {
        if (isset($_POST["post"]["id"]))
            $this->permisosPerfiles = $this->modelo->obtenerPerfilesAcciones($_POST["post"]["id"]);
        #$this->permisosModulo(1002);
        $this->generarArbolPermisos();
    }

    function generarTablaPerfiles()
    {
        $idioma = $this->idioma->$_SESSION["modulo"];
        $permisos = $this->permisos->perfiles;

        $perfiles = $this->modelo->obtenerPerfiles();

        foreach ($perfiles as $perfil) {

            if ($permisos->editar)
                $btnEditar = <<<HTML
<a title="$idioma->btnEditar" class="btn btn-sm btn-default" onclick="navegar('perfiles', 'nuevo', {id: $perfil[idPerfil], modo: 'vistaEditar'});">
    <i class="material-icons">edit</i>
</a>
HTML;
            if ($permisos->eliminar)
                $btnEliminar = <<<HTML
<a title="$idioma->btnEliminar" class="btn btn-sm btn-default" onclick="btnEliminar($perfil[idPerfil]);">
    <i class="material-icons">delete</i>
</a>
HTML;

            $acciones = $btnEditar . $btnEliminar;

            $this->tablaPerfiles .= <<<HTML
<tr>
<td>$perfil[nombrePerfil]</td>
<td class="tdAcciones">$acciones</td>
</tr>
HTML;
        }
    }

    function vistaEditar()
    {
        return $this->modelo->obtenerData($_POST["id"]);
    }

    function generarArbolPermisos($padre = 0)
    {
        $idioma = $this->idioma->modulos;
        $modulos = $this->control->obtenerArbolModulos($padre);
        $this->arbolPermisos .= <<<HTML
<ol class="dd-list">
HTML;

        foreach ($modulos as $modulo) {
            $checked = $this->permisosPerfiles->$modulo['idModulo']->accesar ? "checked" : "";
            if ($padre != 0)
                $divAcciones = <<<HTML
<div class="divAcciones">
        <input $checked type="checkbox" class="js-switch" name="modulos[$modulo[idModulo]][1]" /></div>
HTML;

            $this->arbolPermisos .= <<<HTML
    <li class="dd-item" data-id="$modulo[idModulo]">
        <div class="dd-handle dd-nodrag box">{$idioma->$modulo['idModulo']}
        $divAcciones
        </div>
HTML;
            $this->generarArbolPermisos($modulo['idModulo']);
            if ($padre != 0)
                $this->arbolPermisos .= $this->generarAcciones($modulo['idModulo']);
            $this->arbolPermisos .= <<<HTML
    </li>
HTML;
        }
        $this->arbolPermisos .= <<<HTML
</ol>
HTML;
    }

    function generarAcciones($idModulo)
    {
        $acciones = $this->modelo->obtenerAcciones();
        $htmlAcciones = <<<HTML
<ol class="dd-list">
HTML;
        foreach ($acciones as $accion) {
            $checked = $this->permisosPerfiles->$idModulo->$accion["nombre"] ? "checked" : "";
            $htmlAcciones .= <<<HTML
<li class="dd-item" data-id="$accion[id]">
    <div class="dd-handle dd-nodrag box">$accion[nombre]
        <div class="divAcciones">
            <input $checked type="checkbox" class="js-switch" name="modulos[$idModulo][$accion[id]]"/>
        </div>
    </div>
</li>
HTML;
        }
        $htmlAcciones .= <<<HTML
</ol>
HTML;
        return $htmlAcciones;
    }

    function guardarNuevoPerfil()
    {
        /**
         * @var $modulos
         * @var $nombre
         */
        extract($_POST);
        if ($nombre == "") mensaje_error("Ingrese un nombre para el perfil.");

        $idPerfil = $this->modelo->guardarNuevoPerfil($nombre, $_SESSION["usuario"]);

        foreach ($modulos as $idModulo => $acciones) {
            foreach ($acciones as $idAccion => $value) {
                $this->modelo->guardarPerfilAccion($idPerfil, $idAccion, $idModulo);
            }
        }
    }

    function editarPerfil()
    {
        /**
         * @var $modulos
         * @var $idPerfil
         * @var $nombre
         */
        extract($_POST);
        if ($nombre == "") mensaje_error("Ingrese un nombre para el perfil.");

        $this->modelo->editarNombrePerfil($idPerfil, $nombre);
        $this->modelo->eliminarAccionesPerfil($idPerfil);

        foreach ($modulos as $idModulo => $acciones) {
            foreach ($acciones as $idAccion => $value) {
                $this->modelo->guardarPerfilAccion($idPerfil, $idAccion, $idModulo);
            }
        }
    }

    function eliminarPerfil()
    {
        $this->modelo->eliminarPerfil($_POST["idPerfil"]);
    }
}