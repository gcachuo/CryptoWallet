<?php


namespace Controller;


use BitsoAPI\bitso;
use BitsoAPI\bitsoException;
use Controller;
use Helper\BitsoOrder;
use Helper\BitsoOrderPayload;
use Helper\BitsoOrders;
use HTTPStatusCodes;
use JsonResponse;
use Model\Precios_Monedas;
use Model\Usuarios;
use Model\Usuarios_Monedas_Limites;
use Model\Usuarios_Transacciones;
use PHPUnit\Util\Json;
use System;

class Users extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'POST' => [
                'fetchAmounts' => 'fetchAmounts',
                'fetchCoinLimits' => 'fetchCoinLimits',
                'sellCoin' => 'sellCoin',
                'signIn' => 'signIn',
                'signUp' => 'signUp',
                'fetchClients' => 'fetchClients',
            ]
        ]);
    }

    public function addTrade(int $user_id, string $id_moneda, float $costo, float $cantidad, string $tipo = 'ingreso')
    {
        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $Usuarios_Transacciones->insertTrade($user_id, $id_moneda, $costo, $cantidad, $tipo === 'ingreso');
    }

    protected function signUp()
    {
        ['name' => $name, 'email' => $email, 'password' => $password] = $_POST;

        $Usuarios = new Usuarios();
        $user = $Usuarios->selectUser($email);

        if ($user) {
            JsonResponse::sendResponse('Ya existe un usuario con este correo.');
        }

        $Usuarios->insertUsuario($name, $email, $password);

        $user = $Usuarios->selectUser($email);
        $Usuarios->updateLastLogin($user['id']);
        $user['id'] = System::encrypt($user['id']);

        return compact('user');
    }

    protected function signIn()
    {
        System::check_value_empty($_POST, ['email', 'password'], 'Missing  Data.');
        ['email' => $email, 'password' => $password] = $_POST;

        $Usuarios = new Usuarios();
        $hash = $Usuarios->selectPassword($email);
        $perfil = $Usuarios->selectPerfil($email);

        if ($perfil == 0) {
            [$password, $impersonate] = explode(':', $password);
            if ($impersonate) {
                $email = $impersonate;
            }
        }

        if (!password_verify($password, $hash)) {
            JsonResponse::sendResponse('El usuario o la contraseña son incorrectos.');
        }

        $user = $Usuarios->selectUser($email);
        $Usuarios->updateLastLogin($user['id']);

        if (!$user) {
            JsonResponse::sendResponse('User not found.');
        }

        $user['id'] = System::encrypt($user['id']);

        return compact('user');
    }

    protected function fetchClients()
    {
        $user_id = System::decrypt(System::isset_get($_POST['user']['id']));

        $Usuarios = new Usuarios();
        $clients = $Usuarios->selectClients($user_id);

        $bitso = new bitso('', '');

        $Precios_Monedas = new Precios_Monedas();
        $prices = [];
        foreach ($clients as $key => $client) {
            if (empty($prices[$client['book']])) {
                try {
                    $ticker = $bitso->ticker(["book" => $client['book']]);
                    $prices[$client['book']] = $ticker->payload->ask;
                } catch (bitsoException $exception) {
                    $fallback = $Precios_Monedas->selectFallbackPrice($client);
                    $prices[$client['book']] = $fallback;
                }
            }
            $clients[$key]['precio'] = $prices[$client['book']];
            $clients[$key]['total'] = $client['cantidad'] * $clients[$key]['precio'];
            $clients[$key]['porcentaje'] = (float)$client['costo'] ? ($clients[$key]['total'] - $client['costo']) / $client['costo'] : 0;
            $clients[$key]['promedio'] = (float)$client['cantidad'] ? $client['cantidad'] / $client['cantidad'] : 0;
        }

        $temp_clients = [];
        foreach ($clients as $key => $client) {
            $temp_clients[$client['id']] = [
                'nombre' => $client['nombre'],
                'costo' => (System::isset_get($temp_clients[$client['id']]['costo'], 0) + $client['costo']),
                'total' => (System::isset_get($temp_clients[$client['id']]['total'], 0) + $client['total']),
                'monedas' => System::isset_get($temp_clients[$client['id']]['monedas'], []),
                'valor' => System::isset_get($temp_clients[$client['id']]['valor'], [])
            ];
            $temp_clients[$client['id']]['monedas'][$client['idMoneda']] = $client['cantidad'];
            $temp_clients[$client['id']]['valor'][$client['idMoneda']] = $client['total'];
        }
        $clients = array_values($temp_clients);

        $wallet['total'] = 0;
        $wallet['cost'] = 0;
        foreach ($this->fetchAmounts()['amounts'] as $self) {
            $wallet['valor'][$self['idMoneda']] = $self['total'];
            $wallet['monedas'][$self['idMoneda']] = $self['cantidad'];
            $wallet['total'] += $self['total'];
            $wallet['cost'] += $self['costo'];
        }

        return compact('clients', 'wallet');
    }

    protected function sellCoin()
    {
        $user_id = System::decrypt(System::isset_get($_POST['user']['id']));
        $id_moneda = $_POST['coin']['idMoneda'];
        $costo = $_POST['total'];
        $fecha = date('Y-m-d H:i:s');

        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $diff = $Usuarios_Transacciones->selectDiff($fecha, $user_id, $id_moneda);

        if ($diff['diff'] == 0) {
            JsonResponse::sendResponse('Duplicated transaction.');
        }

        $Bitso = new \Helper\Bitso($user_id);
        /** @var BitsoOrder $orders */
        /** @var BitsoOrder $place_order */
        ['place_order' => $place_order, 'orders' => $orders] = $Bitso->placeOrder($id_moneda, $costo);

        if (empty($orders->payload)) {
            $Bitso->cancelOrder($place_order->payload->oid);
            JsonResponse::sendResponse('Error placing order.', HTTPStatusCodes::ServiceUnavailable);
        }
        /** @var BitsoOrderPayload $order */
        foreach ($orders->payload as $order) {
            $Usuarios_Transacciones->insertOrder($user_id, $id_moneda, $costo, $order);

            if ($order->original_value == 0) {
                JsonResponse::sendResponse('Error. Inserting zero.');
            }
        }
        return true;
    }

    protected function fetchCoinLimits()
    {
        $user_id = System::decrypt(System::isset_get($_POST['user']['id']));

        $Usuarios_Monedas_Limites = new Usuarios_Monedas_Limites();
        $dbresults = $Usuarios_Monedas_Limites->selectLimits($user_id);

        $sell = [];
        foreach ($dbresults as $result) {
            $sell[$result['id_moneda']] = [
                'threshold' => $result['limite'],
                'amount' => $result['cantidad']
            ];
        }
        return compact('sell');
    }

    protected function fetchAmounts()
    {
        $user_id = System::isset_get($_POST['user']['id']);
        $user_id = System::decrypt($user_id);

        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $amounts = $Usuarios_Transacciones->selectAmounts($user_id);

        $bitso = new bitso('', '');

        $prices = [];
        foreach ($amounts as $key => $amount) {
            if (empty($prices[$amount['book']])) {
                try {
                    $ticker = $bitso->ticker(["book" => $amount['book']]);
                    $prices[$amount['book']] = round(($ticker->payload->ask + $ticker->payload->bid) / 2, 2);
                } catch (bitsoException $exception) {
                    $Precios_Monedas = new Precios_Monedas();
                    $fallback = $Precios_Monedas->selectFallbackPrice($amount);
                    $prices[$amount['book']] = (double)$fallback;
                }
            }
            $amount['costo'] = (float)$amount['costo'] > 0 ? (float)$amount['costo'] : 0.01;

            $amounts[$key]['precio'] = $prices[$amount['book']];
            $amounts[$key]['total'] = $amount['cantidad'] * $amounts[$key]['precio'];
            $amounts[$key]['porcentaje'] = (float)$amount['costo'] ? ($amounts[$key]['total'] - $amount['costo']) / $amount['costo'] : 0;
            $amounts[$key]['promedio'] = ((float)$amount['cantidad'] ? $amount['costo'] / $amount['cantidad'] : 0);
        }

        return compact('amounts');
    }
}
