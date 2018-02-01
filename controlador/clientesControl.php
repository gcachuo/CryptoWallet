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
    protected $totalActual, $totalClientes;

    function guardarCompra()
    {
        $bitso = new bitsoConfig();
        try {
            $ticker = $bitso->getTicker($_POST['moneda'] . "_mxn");
        } catch (Exception $ex) {
            $ticker = $bitso->getTicker($_POST['moneda'] . "_btc");
            $btc_mxn = $bitso->getTicker("btc_mxn");
            $ticker->ask = $ticker->ask * $btc_mxn->ask;
        }
        $cantidad = $_POST['pesos'] / $ticker->ask;
        $moneda = $this->modelo->monedas->selectMonedaFromSimbolo($_POST['moneda']);
        $this->modelo->usuario_monedas->comprarMoneda($_POST['id'], $moneda->id, $cantidad, $_POST['pesos']);
    }

    function guardarVenta()
    {
        $bitso = new bitsoConfig();
        try {
            $ticker = $bitso->getTicker($_POST['moneda'] . "_mxn");
        } catch (Exception $ex) {
            $ticker = $bitso->getTicker($_POST['moneda'] . "_btc");
            $btc_mxn = $bitso->getTicker("btc_mxn");
            $ticker->bid = $ticker->bid * $btc_mxn->bid;
        }
        $cantidad = $_POST['pesos'] / $ticker->bid;
        $moneda = $this->modelo->monedas->selectMonedaFromSimbolo($_POST['moneda']);
        $this->modelo->usuario_monedas->venderMoneda($_POST['id'], $moneda->id, $cantidad, $_POST['pesos']);
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
        $totalActual = $this->modelo->obtenerCantidades()['actual'];
        $registros = $this->modelo->obtenerClientes();
        $totalClientes = 0;
        foreach ($registros as $registro) {
            $totalClientes += $registro['actual'];
        }
        $tabla = $this->buildTabla($registros, ["add" => "Comprar", "remove" => "Vender", "cached" => "Rebalancear"]);
        $this->totalActual = $totalActual;
        $this->totalClientes = $totalClientes;
        $this->tablaClientes = $tabla;
        return compact('tabla', 'totalActual', 'totalClientes');
    }
}