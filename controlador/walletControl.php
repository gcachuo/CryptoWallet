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
    public $tablaTransacciones, $tablaMonedas, $operacion, $disponible, $moneda, $totalOriginal, $totalActual;
    private $cliente;

    function editarUsuarioMoneda()
    {
        $this->modelo->usuario_monedas->updateUsuarioMoneda($_POST['idUsuario'], $_POST['idMoneda'], $_POST['cantidad'], $_POST['costo']);
    }

    function confirmarMovimiento()
    {
        $bitso = new bitsoConfig();
        $bitso->crearOrden($_POST['book'], $_POST['monto'], $_POST['precio'], $_POST['tipo']);
    }

    protected function cargarPrincipal()
    {
        $this->cliente = $this->modelo->clientes->selectClienteFromId($_SESSION['usuario']);
        $this->obtenerDisponible();
        $this->buildTablaMonedas();
        header("Refresh: 300;"); #300 / 60 = 5min
    }

    function obtenerDisponible()
    {
        $bitso = new bitsoConfig();
        $balance = $bitso->getBalance();
        $this->disponible = $balance[5];
        if ($this->disponible->total != $_SESSION['disponible']) {
            Globales::send_notification("bitso: " . round($this->disponible->total, 2));
            $_SESSION['disponible'] = $this->disponible->total;
        }
    }

    function buildtablaMonedas()
    {
        $monedas = $this->modelo->monedas->selectMonedas();
        foreach ($monedas as $moneda) {
            $coin = $this->cargarMoneda($moneda['simbolo']);
            $monto = abs(str_replace(',', '', substr($coin->ganancia, 1)));
            $precio = str_replace(',', '', substr($coin->ticker, 1));
            $bitso = new bitsoConfig();
            $color = "none";
            if (abs($coin->porcentaje) >= 2 and $coin->porcentaje <= 4) {
                if ($coin->porcentaje < 0 and !$bitso->getActive('buy', $moneda['book'])) {
                    $color = "lightpink";
                    $btnCompra = <<<HTML
<a title="Compra" onclick="aside('wallet','compra_venta',{id:'$moneda[simbolo]',monto:'$monto',precio:'$precio',mode:'buy'})" class="btn btn-sm btn-default">
    <i class="material-icons">file_download</i>
</a>
HTML;
                } elseif ($coin->porcentaje > 0 and !$bitso->getActive('sell', $moneda['book'])) {
                    $color = "lightgreen";
                    $btnVenta = <<<HTML
<a title="Venta" onclick="aside('wallet','compra_venta',{id:'$moneda[simbolo]',monto:'$monto',precio:'$precio',mode:'sell'})" class="btn btn-sm btn-default">
    <i class="material-icons">file_upload</i>
</a>
HTML;
                }
                if ($color != "none")
                    echo <<<HTML
<script>
    var moneda=[];
    moneda.nombre = '$moneda[nombre]';
    moneda.ganancia = '$coin->ganancia';
    moneda.porcentaje = '$coin->porcentaje';
</script>
HTML;
            } elseif ($coin->porcentaje > 4) {
                $cantidadmoneda = $this->modelo->usuario_monedas->selectCantidad($_SESSION['usuario'], $moneda['id']);
                $valormoneda = str_replace(',', '', substr($coin->valor, 1));
                $this->modelo->usuario_monedas->updateUsuarioMoneda($_SESSION["usuario"], $moneda['id'], $cantidadmoneda, $valormoneda);
            }
            $acciones = <<<HTML
$btnCompra
$btnVenta
<a title="Historial" onclick="aside('wallet','trades',{id:'$moneda[simbolo]'})" class="btn btn-sm btn-default">
    <i class="material-icons">format_list_bulleted</i>
</a>
<a title="Editar" onclick="btnEditar('$moneda[simbolo]')" class="btn btn-sm btn-default"><i class="material-icons">edit</i></a>
HTML;

            $this->tablaMonedas .= <<<HTML
<tr style="background:$color">
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

    /**
     * @param $simbolo
     * @return object
     */
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

        if ($simbolo == "eth")
            try {
                $cantidad += number_format(round($this->balanceNanoPool(), 8), 8);
            } catch (Exception $ex) {
                echo "<script>console.log('{$ex->getMessage()}')</script>";
            }

        if ($cantidad == 0) $costo = 0;
        else $costo = $invertido / $cantidad;
        $valor = $cantidad * $ticker;

        $ganancia = $valor - $invertido;
        if ($invertido == 0) $porcentaje = 0;
        else $porcentaje = ($ganancia / $invertido) * 100;

        $this->totalOriginal += $invertido;
        $this->totalActual += $valor;

        Globales::formato_moneda("$", $costo);
        Globales::formato_moneda("$", $invertido);
        Globales::formato_moneda("$", $ticker);
        Globales::formato_moneda("$", $valor);
        Globales::formato_moneda("$", $ganancia);
        Globales::formato_moneda("", $porcentaje);

        return (object)compact("ticker", "cantidad", "costo", "invertido", "valor", "ganancia", "porcentaje");
    }

    function balanceNanoPool()
    {
        $address = $this->cliente->direccionEth;//"0xa6edd791405f49021a7e7096c036cff0ce6e085a";
        $nanopool = Globales::url_request('PUBLIC', "https://api.nanopool.org/v1/eth/balance/$address", 'GET');
        $balance = json_decode($nanopool)->data;
        #Globales::send_notification("nanopool: " . number_format(round($balance, 8), 8));
        return $balance;
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
            case "editar":
                $this->moneda = $this->modelo->monedas->selectMonedaFromSimbolo($_POST['simbolo']);
                $this->moneda->cantidad = $this->modelo->usuario_monedas->selectCantidad($_SESSION['usuario'], $this->moneda->id);
                $this->moneda->costo = $this->modelo->usuario_monedas->selectCosto($_SESSION['usuario'], $this->moneda->id);
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
}