<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 04/08/2017
 * Time: 01:52 PM
 * @property ModeloWallet modelo
 */

class Wallet extends Control
{
    public $bitcoin, $ethereum, $ripple;
    private $api_key, $api_secret;

    protected function cargarPrincipal()
    {
        $api = $this->modelo->usuario_llaves->selectApiKey($_SESSION['usuario']);
        $this->api_key = $api->apiKey;
        $this->api_secret = $api->apiSecret;

        $this->cargarBitcoin();
    }

    protected function cargarAside()
    {
        // TODO: Implement cargarAside() method.
    }

    function cargarBitcoin()
    {
        /**
         * 0 = btc
         * 1 = etc
         * 2 = eth
         * 3 = xrp
         * 4 = mxn
         */
        $this->bitcoin = $this->cargarMoneda('btc_mxn', 0);
        $this->ethereum = $this->cargarMoneda('eth_mxn', 2);
        $this->ripple = $this->cargarMoneda('xrp_mxn', 3);
    }

    function cargarMoneda($book, $index)
    {
        try {
            $bitso = new bitsoConfig($this->api_key, $this->api_secret);
            $ticker = $bitso->getTicker($book)->ask;
            $balance = $bitso->getBalance();

            $invertido = $this->modelo->usuario_monedas->selectCosto($_SESSION['usuario'], $index);
            $cantidad = $this->modelo->usuario_monedas->selectCantidad($_SESSION['usuario'], $index);
            $cantidad += $balance[$index]->total;

            if ($cantidad == 0) $costo = 0;
            else $costo = $invertido / $cantidad;
            $valor = $cantidad * $ticker;
            $ganancia = $valor - $invertido;
            if ($invertido == 0) $porcentaje = 0;
            else $porcentaje = $ganancia / $invertido;

            Globales::formato_moneda("$", $costo);
            Globales::formato_moneda("$", $invertido);
            Globales::formato_moneda("$", $ticker);
            Globales::formato_moneda("$", $valor);
            Globales::formato_moneda("$", $ganancia);

            return (object)compact("ticker", "cantidad", "costo", "invertido", "valor", "ganancia", "porcentaje");
        } catch (Exception $ex) {
            echo "<script>alert('{$ex->getMessage()}')</script>";
        }
    }
}