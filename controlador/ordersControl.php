<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 24/08/2017
 * Time: 02:01 PM
 */

/** @property ModeloOrders $modelo */
class Orders extends Control
{

    public $tablaOrdenes;

    protected function cargarPrincipal()
    {
        $this->buildTablaOrdenes();
    }

    protected function cargarAside()
    {
        // TODO: Implement cargarAside() method.
    }

    private function buildTablaOrdenes()
    {
        $bitso = new bitsoConfig();
        $monedas = $this->modelo->monedas->selectMonedas();
        foreach ($monedas as $moneda) {
            $ordenes = $bitso->getOrders($moneda[book]);
            foreach ($ordenes as $orden) {
                $acciones = <<<HTML
<a onclick="btnEliminar('{$orden->oid}')" class="btn btn-default btn-sm"><i class="material-icons">delete</i></a>
HTML;

                $this->tablaOrdenes .= <<<HTML
<tr>
<td>{$orden->original_value}</td>
<td>{$orden->unfilled_amount}</td>
<td>{$orden->book}</td>
<td>{$orden->side}</td>
<td>{$orden->price}</td>
<td class="tdAcciones">$acciones</td>
</tr>
HTML;

            }
        }
    }

    function eliminarOrden()
    {
        $bitso = new bitsoConfig();
        $bitso->deleteOrder($_POST['id']);
    }
}