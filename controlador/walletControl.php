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

    function cargarGraficas()
    {
        $bitso = new bitsoConfig();
        $data = ["balance" => ["names" => [], "values" => []]];
        $monedas = $this->modelo->monedas->selectMonedas();
        foreach ($monedas as $moneda) {
            $ticker = $bitso->getTicker($moneda['book'])->ask;
            $explode = explode("_", $moneda['book']);
            if ($explode[1] == "btc") {
                $tickerbtc = $bitso->getTicker("btc_mxn")->ask;
                $ticker = $ticker * $tickerbtc;
            }
            $coin = $this->cargarMoneda($moneda['simbolo']);
            array_push($data['balance']['names'], $moneda["nombre"]);
            array_push($data['balance']['values'], ["name" => $moneda["nombre"], "value" => ($coin->cantidad * $ticker)]);
        }
        $data['colores'] = [
            ['red' => 76, 'green' => 202, 'blue' => 71],
            ['red' => 247, 'green' => 147, 'blue' => 23],
            ['red' => 130, 'green' => 131, 'blue' => 132],
            ['red' => 190, 'green' => 190, 'blue' => 190],
            ['red' => 0, 'green' => 164, 'blue' => 225]
        ];

        $data['balance']['names'] = array_reverse(array_unique(array_reverse($data['balance']['names'])));
        $data['balance']['values'] = array_values($data['balance']['values']);

        return $data;
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

    function editarUsuarioMoneda()
    {
        $this->modelo->usuario_monedas->updateUsuarioMoneda($_POST['idUsuario'], $_POST['idMoneda'], $_POST['cantidad'], str_replace(',', '', $_POST['costo']));
    }

    function confirmarMovimiento()
    {
        $bitso = new bitsoConfig();
        $bitso->crearOrden($_POST['book'], $_POST['monto'], $_POST['precio'], $_POST['tipo']);
    }

    protected function cargarPrincipal()
    {
        $monedas = $this->modelo->monedas->selectMonedas();
        foreach ($monedas as $key => $moneda) {
            $coin = $this->cargarMoneda($moneda['simbolo']);
            $this->totalOriginal += $coin->invertido;
            $this->totalActual += $coin->valor;
            $monedas[$key]['coin'] = $coin;
        }
        $this->cliente = $this->modelo->clientes->selectClienteFromId($_SESSION['usuario']);
        $this->obtenerDisponible();
        $this->buildTablaMonedas($monedas);
        header("Refresh: 300;"); #300 / 60 = 5min
    }

    function obtenerDisponible()
    {
        $bitso = new bitsoConfig();
        $balance = $bitso->getBalance();
        $this->disponible = $balance[6];
        if ($this->disponible->total != $_SESSION['disponible']) {
            Globales::send_notification("bitso: " . round($this->disponible->total, 2));
            $_SESSION['disponible'] = $this->disponible->total;
        }
    }

    function buildtablaMonedas($monedas)
    {
        $tabla = "";
        foreach ($monedas as $moneda) {
            $coin = $moneda['coin'];
            $monto = abs(str_replace(',', '', substr($coin->ganancia, 1)));
            $precio = str_replace(',', '', substr($coin->ticker, 1));
            $bitso = new bitsoConfig();
            $color = "none";

            $porcentaje = ($coin->valor / $this->totalActual) * 100;
            $coin->ganancia = $this->obtenerPorcentajeDelta($porcentaje, $moneda);

            Globales::formato_moneda("$", $coin->costo);
            Globales::formato_moneda("$", $coin->invertido);
            Globales::formato_moneda("$", $coin->ticker);
            Globales::formato_moneda("$", $coin->valor);
            Globales::formato_moneda("$", $coin->ganancia);
            Globales::formato_moneda("", $coin->porcentaje);
            Globales::formato_moneda("", $porcentaje);

            if (abs($coin->porcentaje) >= 3) {
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
            }
            $acciones = <<<HTML
$btnCompra
$btnVenta
<a title="Historial" onclick="aside('wallet','trades',{id:'$moneda[simbolo]'})" class="btn btn-sm btn-default">
    <i class="material-icons">format_list_bulleted</i>
</a>
<a title="Editar" onclick="btnEditar('$moneda[simbolo]')" class="btn btn-sm btn-default"><i class="material-icons">edit</i></a>
HTML;

            $tabla .= <<<HTML
<tr style="background:$color">
    <td>$moneda[nombre]</td>
    <td>$coin->cantidad</td>
    <td>$coin->invertido</td>
    <td>$coin->costo</td>
    <td>$coin->ticker</td>
    <td>$coin->valor</td>
    <td>$coin->ganancia</td>
    <td>$porcentaje%</td>
    <td>$coin->porcentaje%</td>
    <td class="tdAcciones">
       $acciones
    </td>
</tr>
HTML;
            unset($btnCompra);
            unset($btnVenta);
        }
        $this->tablaMonedas = $tabla;
        return compact("tabla");
    }

    private function obtenerPorcentajeDelta($porcentaje, $moneda)
    {
        $file = $this->getPorcentajeFromFile($moneda['simbolo']);
        if ($file == 0) {
            $ganancia = $moneda['coin']->valor - $moneda['coin']->invertido;
        } else {
            $diferencia = $porcentaje - $file;
            $valor = ($this->totalActual * $file) / 100;
            $this->modelo->updateOriginal($moneda, $valor);
            $ganancia = $this->totalActual * ($diferencia / 100);
        }
        return $ganancia;
    }

    private function getPorcentajeFromFile($simbolo)
    {
        $usuario = $_SESSION["usuario"];
        $path = "recursos/config/$usuario.json";
        if (!file_exists($path)) {
            $array = [];
            foreach ($this->modelo->monedas->selectMonedas() as $moneda) {
                $array[$moneda['simbolo']] = 0;
            }
            $json = json_encode($array);
            $fp = fopen($path, "wb");
            fwrite($fp, $json);
            fclose($fp);
        }
        $object = json_decode(file_get_contents($path));
        $porcentaje = $object->$simbolo;
        return $porcentaje;
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