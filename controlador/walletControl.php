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
    public $bitcoincash, $bitcoin, $ethereum, $ripple;
    private $api_key, $api_secret;

    protected function cargarPrincipal()
    {
        $api = $this->modelo->usuario_llaves->selectApiKey($_SESSION['usuario']);
        $this->api_key = $api->apiKey;
        $this->api_secret = $api->apiSecret;

        /**
         * 0 = bch
         * 1 = btc
         * 2 = etc
         * 3 = eth
         * 4 = xrp
         * 5 = mxn
         */
        $this->bitcoincash = $this->cargarMoneda('bch_btc', 0);
        $this->bitcoin = $this->cargarMoneda('btc_mxn', 1);
        $this->ethereum = $this->cargarMoneda('eth_mxn', 3);
        $this->ripple = $this->cargarMoneda('xrp_mxn', 4);


    }

    protected function cargarAside()
    {
        // TODO: Implement cargarAside() method.
    }

    function cargarMoneda($book, $index)
    {
        try {
            $bitso = new bitsoConfig($this->api_key, $this->api_secret);
            $ticker = $bitso->getTicker($book)->ask;
            $explode = explode("_", $book);
            if ($explode[1] == "btc") {
                $tickerbtc = $bitso->getTicker("btc_mxn")->ask;
                $ticker = $ticker * $tickerbtc;
            }
            $balance = $bitso->getBalance();
        } catch (Exception $ex) {
            echo "<script>alert('{$ex->getMessage()}')</script>";
        }

        $invertido = $this->modelo->usuario_monedas->selectCosto($_SESSION['usuario'], $index);
        $cantidad = $this->modelo->usuario_monedas->selectCantidad($_SESSION['usuario'], $index);
        $cantidad += $balance[$index]->total;

        if ($cantidad == 0) $costo = 0;
        else $costo = $invertido / $cantidad;
        $valor = $cantidad * $ticker;

        $ganancia = $valor - $invertido;
        if ($invertido == 0) $porcentaje = 0;
        else $porcentaje = ($ganancia / $invertido) * 100;

        Globales::formato_moneda("$", $costo);
        Globales::formato_moneda("$", $invertido);
        Globales::formato_moneda("$", $ticker);
        Globales::formato_moneda("$", $valor);
        Globales::formato_moneda("$", $ganancia);
        Globales::formato_moneda("", $porcentaje);

        return (object)compact("ticker", "cantidad", "costo", "invertido", "valor", "ganancia", "porcentaje");
    }
}