<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 27/feb/2017
 * Time: 05:10 PM
 */

/**
 * @property TablaModulos modulos
 * @property TablaUsuarios usuarios
 * @property TablaEstados estados
 * @property TablaCiudades ciudades
 * @property TablaPerfiles_Acciones perfiles_acciones
 * @property distribuidor\TablaCbiz_Cliente cbiz_cliente
 * @property distribuidor\TablaContacto_Cliente contacto_cliente
 * @property distribuidor\TablaSuscripciones suscripciones
 */
class ModeloControl extends Tabla
{
    /**
     * @return object
     */
    function obtenerModulos()
    {
        $objectModulos = (object)array();
        $modulos = $this->modulos->selectModulos();
        foreach ($modulos as $modulo) {
            $objectModulos->$modulo["idModulo"] = $modulo;
        }
        return $objectModulos;
    }

    function obtenerArbolModulos($padre)
    {
        return $this->modulos->selectModulosFromParent($padre);
    }


    /**
     * @return object
     */
    function obtenerPermisosModulo()
    {
        $perfil = $this->usuarios->selectPerfil($_SESSION["usuario"] ?: '');
        $arrayAcciones = $this->perfiles_acciones->selectAccionesFromPerfil($perfil ?: '');
        return $arrayAcciones;
    }

    function obtenerNombreModulo($idModulo)
    {
        return $this->modulos->selectNombreModuloFromId($idModulo);
    }

    function obtenerEstados()
    {
        return $this->estados->selectEstados();
    }

    function obtenerCiudades($idEstado)
    {
        return $this->ciudades->selectCiudadesFromEstado($idEstado);
    }

    function obtenerDiasRestantes($idUsuario)
    {
        $usuario = $this->usuarios->selectUsuarioFromId($idUsuario);
        Globales::setNamespace("distribuidor");
        $idCliente = $this->contacto_cliente->selectIdClienteFromCorreo($usuario->correo);
        $estatus = (bool)$this->suscripciones->selectEstatusSuscripcion($idCliente);
        if ($estatus) {
            Globales::setNamespace("");
            return -1;
        }
        $fecha = $this->cbiz_cliente->selectFechaCreacion($idCliente);
        Globales::setNamespace("");

        $today = date_create(date("Y-m-d"));
        $start = date_create($fecha);
        date_add($start, date_interval_create_from_date_string('5 days'));
        $intervalo = date_diff($start, $today);
        $dias = $intervalo->days;
        if ($today > $start) {
            $dias = 0;
        }
        return $dias;
    }
}