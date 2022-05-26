<?php


namespace Controller;


use Controller;
use CoreException;
use Model\Monedas;
use System;

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

    /**
     * @return array
     * @throws CoreException
     */
    function selectCoins(): array
    {
        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $Monedas = new Monedas();
        $monedas = $Monedas->selectMonedas($user_id);

        return compact('monedas');
    }
}
