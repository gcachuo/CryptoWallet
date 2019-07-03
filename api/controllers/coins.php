<?php


class coins
{
    function getTotal()
    {
        $costo = isset_get($_POST['costo']);
        $precio = isset_get($_POST['precio']);
        $cantidad = isset_get($_POST['cantidad']);

        $fee = isset_get($_POST['fee'], 0.5);

        if (!$precio) {
            return false;
        }

        switch (true) {
            case empty($cantidad):
                $cantidad = round($costo / $precio, 8);
                $total['coin'] = round($cantidad - $cantidad / 100 * $fee, 8);
                break;
            case empty($costo):
                $costo = $cantidad * $precio;
                $total['fiat'] = $costo - $precio * $fee;
                break;
        }

        return compact('total');
    }
}