<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 12/01/2018
 * Time: 10:14 AM
 */

/**
 * @property ModeloClientes modelo
 */
class Clientes extends Control
{

    protected $tablaClientes;

    protected function cargarAside()
    {
        // TODO: Implement cargarAside() method.
    }

    protected function cargarPrincipal()
    {
        $this->buildTablaClientes();
    }

    private function buildTablaClientes()
    {
        $registros = $this->modelo->obtenerClientes();
        $tabla = $this->buildTabla($registros, ["add" => "Comprar", "remove" => "Vender", "cached" => "Rebalancear"]);
        $this->tablaClientes = $tabla;
        return compact('tabla');
    }
}