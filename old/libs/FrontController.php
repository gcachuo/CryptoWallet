<?php

/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 05/abr/2017
 * Time: 12:14 PM
 */
class FrontController
{
    static function main()
    {
        require 'libs/View.php'; //Mini motor de plantillas

        $vista = new View();
        $vista
            ->show('balance')
            ->show('operations')
            ->show('orders')
            ->show('ticker')
            ->show('address')
            ->show('transactions');
    }
}