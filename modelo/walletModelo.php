<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 04/08/2017
 * Time: 04:43 PM
 */

/**
 * @property TablaUsuario_Llaves usuario_llaves
 * @property TablaUsuario_Monedas usuario_monedas
 * @property TablaMonedas monedas
 * @property TablaClientes clientes
 */
class ModeloWallet extends Tabla
{
    public function updateOriginal($moneda, $nuevovalor)
    {
        $cantidadmoneda = $this->usuario_monedas->selectCantidad($_SESSION['usuario'], $moneda['id']);
        $this->usuario_monedas->updateUsuarioMoneda($_SESSION["usuario"], $moneda['id'], $cantidadmoneda, $nuevovalor);
    }
}