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
    public $tablaTransacciones, $tablaMonedas, $operacion, $disponible;

    protected function cargarPrincipal()
    {
        $this->obtenerDisponible();
        $this->buildTablaMonedas();
        Globales::send_notification($this->disponible);
        header("Refresh: 300;");
    }

    function obtenerDisponible()
    {
        $bitso = new bitsoConfig();
        $balance = $bitso->getBalance();
        $this->disponible = $balance[5];
    }

    function buildtablaMonedas()
    {
        $monedas = $this->modelo->monedas->selectMonedas();
        foreach ($monedas as $moneda) {
            $coin = $this->cargarMoneda($moneda['simbolo']);
            $monto = abs(substr($coin->ganancia, 1));
            $precio = str_replace(',', '', substr($coin->ticker, 1));
            $bitso = new bitsoConfig();
            if ($coin->porcentaje < 0 and !$bitso->getActive('buy', $moneda['book']))
                $btnCompra = <<<HTML
<a title="Compra" onclick="aside('wallet','compra_venta',{id:'$moneda[simbolo]',monto:'$monto',precio:'$precio',mode:'buy'})" class="btn btn-sm btn-default">
    <i class="material-icons">file_download</i>
</a>
HTML;
            elseif ($coin->porcentaje > 0 and !$bitso->getActive('sell', $moneda['book']))
                $btnVenta = <<<HTML
<a title="Venta" onclick="aside('wallet','compra_venta',{id:'$moneda[simbolo]',monto:'$monto',precio:'$precio',mode:'sell'})" class="btn btn-sm btn-default">
    <i class="material-icons">file_upload</i>
</a>
HTML;

            $acciones = <<<HTML
$btnCompra
$btnVenta
<a title="Historial" onclick="aside('wallet','trades',{id:'$moneda[simbolo]'})" class="btn btn-sm btn-default">
    <i class="material-icons">format_list_bulleted</i>
</a>
HTML;

            $this->tablaMonedas .= <<<HTML
<tr>
    <td>$moneda[nombre]</td>
    <td>$coin->cantidad</td>
    <td>$coin->invertido</td>
    <td>$coin->costo</td>
    <td>$coin->ticker</td>
    <td>$coin->valor</td>
    <td>$coin->ganancia</td>
    <td>$coin->porcentaje</td>
    <td class="tdAcciones">
       $acciones
    </td>
</tr>
HTML;
            unset($btnCompra);
            unset($btnVenta);
        }
    }

    protected function cargarAside()
    {
        switch ($_POST['asideAccion']) {
            case "trades":
                $this->cargarTransacciones($_POST['id']);
                break;
            case "compra_venta":
                $this->operacion->moneda = $this->modelo->monedas->selectMonedaFromSimbolo($_POST['id']);
                $this->operacion->monto = $_POST['monto'];
                $this->operacion->precio = $_POST['precio'];
                $this->operacion->tipo = $_POST['mode'];
                $this->operacion->comision = $_POST['monto'] * 0.01;
                $this->operacion->total = $_POST['monto'] - $this->operacion->comision;
                break;
        }
    }

    function confirmarMovimiento()
    {
        $bitso = new bitsoConfig();
        $bitso->crearOrden($_POST['book'], $_POST['monto'], $_POST['precio'], $_POST['tipo']);
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