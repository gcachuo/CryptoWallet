<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 12/01/2018
 * Time: 10:22 AM
 */

/**
 * @property TablaClientes clientes
 * @property TablaMonedas monedas
 * @property TablaUsuario_Monedas usuario_monedas
 */
class ModeloClientes extends Modelo
{

    public function obtenerClientes()
    {
        require_once "config/bitsoConfig.php";
        $bitso = new bitsoConfig();
        $clientesSQL = $this->clientes->selectClientesFromUser($_SESSION['usuario']);
        $clientes = [];
        foreach ($clientesSQL as $key => $cliente) {
            $clientes[$cliente['id']] = $cliente;
            $cantidades = $this->obtenerCantidadesCliente($bitso, $cliente['id']);
            $clientes[$cliente['id']] = array_merge($clientes[$cliente['id']], $cantidades);
            unset($clientes[$cliente['id']]['id']);
        }
        return $clientes;
    }

    /**
     * @param bitsoConfig $bitso
     * @param $idCliente
     * @return array
     */
    function obtenerCantidadesCliente($bitso, $idCliente)
    {
        $cliente = $ticker = [];
        $actual = 0;
        $monedas = $this->monedas->selectMonedas();
        $cantidadBitso = $bitso->getBalance();
        foreach ($monedas as $key => $moneda) {
            $book = $moneda['book'];
            if (!isset($ticker[$book])) {
                $ticker[$book] = $bitso->getTicker($book);
            }
            $pair = explode("_", $book);
            if ($pair[1] != "mxn") {
                $book2 = $pair[1] . "_mxn";
                $ticker[$book2] = $bitso->getTicker($book2);
                $ticker[$pair[0] . "_mxn"]->ask = $ticker[$book]->ask * $ticker[$book2]->ask;
                $book = $pair[0] . "_mxn";
            }
            $simbolo = $moneda['simbolo'];
            $ask = $ticker[$book]->ask;
            $cantidad = $this->usuario_monedas->selectCantidad($idCliente, $moneda['id']) + $cantidadBitso[$moneda['id']]->total ?: 0;
            $monto = $cantidad * $ask;
            $cliente[$simbolo] = round($monto, 2);
            $actual += $monto;
        }
        $cliente['actual'] = round($actual, 2);
        return $cliente;
    }

    public function obtenerCantidades()
    {
        require_once "config/bitsoConfig.php";
        $bitso = new bitsoConfig();
        $cantidades = $this->obtenerCantidadesCliente($bitso, $_SESSION['usuario']);
        return $cantidades;
    }
}