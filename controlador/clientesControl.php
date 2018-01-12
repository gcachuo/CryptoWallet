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
    protected $listaMonedas;

    function guardarCompra()
    {
        $bitso = new bitsoConfig();
        $ticker = $bitso->getTicker($_POST['moneda'] . "_mxn");
        $cantidad = $_POST['pesos'] / $ticker->ask;
        $moneda = $this->modelo->monedas->selectMonedaFromSimbolo($_POST['moneda']);
        $this->modelo->usuario_monedas->comprarMoneda($_POST['id'], $moneda->id, $cantidad, $_POST['pesos']);
    }

    protected function cargarAside()
    {
        $this->listaMonedas = $this->buildLista($this->modelo->monedas->selectListaMonedas());
    }

    protected function cargarPrincipal()
    {
        $this->buildTablaClientes();
    }

    function buildTablaClientes()
    {
        $registros = $this->modelo->obtenerClientes();
        $tabla = $this->buildTabla($registros, ["add" => "Comprar", "remove" => "Vender", "cached" => "Rebalancear"]);
        $this->tablaClientes = $tabla;
        return compact('tabla');
    }
}