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

    public $tablaTransacciones;

    protected function cargarPrincipal()
    {

        /**
         * 0 = bch
         * 1 = btc
         * 2 = etc
         * 3 = eth
         * 4 = xrp
         * 5 = mxn
         */
        $this->bitcoincash = $this->cargarMoneda('bch');
        $this->bitcoin = $this->cargarMoneda('btc');
        $this->ethereum = $this->cargarMoneda('eth');
        $this->ripple = $this->cargarMoneda('xrp');

    }

    protected function cargarAside()
    {
        switch ($_POST['asideAccion']) {
            case "trades":
                $this->cargarTransacciones($_POST['id']);
                break;
        }
    }

    function cargarTransacciones($simbolo)
    {
        $moneda = $this->modelo->monedas->selectMonedaFromSimbolo($simbolo);
        $bitso = new bitsoConfig();
        $transacciones = $bitso->getTrades($moneda->book);
        foreach ($transacciones as $transaccion) {
            $fecha = Globales::convertir_formato_fecha($transaccion->created_at, "Y-m-d\TH:i:sO", "d/m/Y h:i:sa");
            $transaccion->major = abs($transaccion->major);
            $transaccion->minor = abs($transaccion->minor);
            $compra = $transaccion->fees_currency == $transaccion->major_currency ? $transaccion->major - $transaccion->fees_amount : $transaccion->minor - $transaccion->fees_amount;
            $precioFinal = $transaccion->side == "buy" ? ($transaccion->minor / $compra) : ($compra / $transaccion->major);
            if ($transaccion->fees_currency == $simbolo) {
                $pago = $compra * $precioFinal;
                Globales::formato_moneda("$", $pago);
            } else {
                $pago = ($compra / $precioFinal) . " $simbolo";
            }
            Globales::formato_moneda("", $precioFinal);

            $this->tablaTransacciones .= <<<HTML
<tr>
<td>$fecha</td>
<td>{$transaccion->side}</td>
<td>$compra {$transaccion->fees_currency}</td>
<td>$pago</td>
<td>$precioFinal</td>
</tr>
HTML;
        }
    }

    function cargarMoneda($simbolo)
    {
        $moneda = $this->modelo->monedas->selectMonedaFromSimbolo($simbolo);
        try {
            $bitso = new bitsoConfig();
            $ticker = $bitso->getTicker($moneda->book)->ask;
            $explode = explode("_", $moneda->book);
            if ($explode[1] == "btc") {
                $tickerbtc = $bitso->getTicker("btc_mxn")->ask;
                $ticker = $ticker * $tickerbtc;
            }
            $balance = $bitso->getBalance();
        } catch (Exception $ex) {
            echo "<script>console.log('{$ex->getMessage()}')</script>";
        }

        $invertido = $this->modelo->usuario_monedas->selectCosto($_SESSION['usuario'], $moneda->id);
        $cantidad = $this->modelo->usuario_monedas->selectCantidad($_SESSION['usuario'], $moneda->id);
        $cantidad += $balance[$moneda->id]->total;

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