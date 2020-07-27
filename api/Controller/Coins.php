<?php


namespace Controller;


use Controller;
use Model\Monedas;

class Coins extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'GET' => [
                'list' => 'selectCoins'
            ]
        ]);
    }

    function selectCoins(){
        $Monedas = new Monedas();
        $monedas = $Monedas->selectMonedas();

        return compact('monedas');
    }
}
