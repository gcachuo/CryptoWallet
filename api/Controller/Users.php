<?php

namespace Controller;

use BitsoAPI\bitsoException;
use Controller;
use CoreException;
use Helper\Bitso;
use Helper\BitsoOrder;
use Helper\BitsoOrderPayload;
use HTTPStatusCodes;
use JsonResponse;
use Model\Precios_Monedas;
use Model\Usuarios;
use Model\Usuarios_Monedas_Limites;
use Model\Usuarios_Notificaciones;
use Model\Usuarios_Transacciones;
use System;

class Users extends Controller
{
    public function __construct()
    {
        parent::__construct([
            'PUT' => [
                'trade' => 'addTrade'
            ],
            'POST' => [
                'fetchAmounts' => 'fetchAmounts',
                'fetchCoinLimits' => 'fetchCoinLimits',
                'sellCoin' => 'sellCoin',
                'signIn' => 'signIn',
                'login' => 'signIn',
                'signUp' => 'signUp',
                'fetchClients' => 'fetchClients',
                'setCoinLimit' => 'setCoinLimit',
            ],
            'GET' => [
                'notifications' => 'getNotifications'
            ]
        ]);
    }

    /**
     * @return string[][]
     * @throws CoreException
     */
    protected function getNotifications(): array
    {
        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $Usuarios = new Usuarios_Notificaciones();
        return $Usuarios->selectRows($user_id);
    }

    protected function addTrade()
    {
        global $_PUT;
        ['user_token' => $user_token, 'id_moneda' => $id_moneda, 'costo' => $costo, 'cantidad' => $cantidad, 'tipo' => $tipo] = $_PUT;

        $user = System::decode_token($user_token);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

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

    /**
     * @return array
     * @throws CoreException
     */
    protected function signIn(): array
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

        $token = System::encode_token($user);
        return compact('user', 'token');
    }

    protected function fetchClients()
    {
        System::check_value_empty($_POST, ['user_token']);
        $user = System::decode_token($_POST['user_token']);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $Usuarios = new Usuarios();
        $clients = $Usuarios->selectClients($user_id);

        $bitso = new Bitso($user_id);

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

    /**
     * @throws CoreException
     */
    protected function sellCoin()
    {
        System::check_value_empty($_POST, ['user_token', 'coin', 'total']);
        $user = System::decode_token($_POST['user_token']);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $id_moneda = $_POST['coin']['idMoneda'];
        $costo = $_POST['total'];
        $fecha = date('Y-m-d H:i:s');

        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $diff = $Usuarios_Transacciones->selectDiff($fecha, $user_id, $id_moneda);

        if ($diff['diff'] == 0) {
            throw new CoreException('Duplicated transaction.', 400);
        }

        try {
            $Bitso = new Bitso($user_id);

            if(!$Bitso->isKeySet()){
                return false;
            }

            /** @var BitsoOrder $orders */
            /** @var BitsoOrder $place_order */
            ['place_order' => $place_order, 'orders' => $orders] = $Bitso->placeOrder($id_moneda, $costo);
        } catch (CoreException $exception) {
            if ($exception->getCode() != 503) {
                throw $exception;
            }
            throw new CoreException($exception->getMessage(), HTTPStatusCodes::BadRequest);
        }

        if (empty($orders->payload)) {
            $Bitso->cancelOrder($place_order->payload->oid);
            throw new CoreException('Error placing order.', HTTPStatusCodes::ServiceUnavailable);
        }
        /** @var BitsoOrderPayload $order */
        foreach ($orders->payload as $order) {
            $Usuarios_Transacciones->insertOrder($user_id, $order->oid);

            if ($order->original_value == 0) {
                throw new CoreException('Error. Inserting zero.', HTTPStatusCodes::BadRequest);
            }
        }
        return true;
    }

    protected function fetchCoinLimits()
    {
        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $Usuarios_Monedas_Limites = new Usuarios_Monedas_Limites();
        $dbresults = $Usuarios_Monedas_Limites->selectLimits($user_id);

        $sell = [];
        $buy = [];
        foreach ($dbresults as $result) {
            $sell[$result['id_moneda']] = [
                'threshold' => $result['limite'],
                'amount' => $result['cantidad']
            ];
            $buy[$result['id_moneda']] = [
                'threshold' => $result['limite'],
                'amount' => $result['cantidad']
            ];
        }
        return compact('sell', 'buy');
    }

    /**
     * @throws CoreException
     */
    protected function fetchAmounts()
    {
        $user = System::decode_token(USER_TOKEN);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $Usuarios_Transacciones = new Usuarios_Transacciones();
        $amounts = $Usuarios_Transacciones->selectAmounts($user_id);
        $avgs = $Usuarios_Transacciones->selectBuyPriceAvg($user_id);

        $bitso = new Bitso($user_id);
        $_bitso = new \BitsoAPI\bitso('', '');

        $limits = $this->fetchCoinLimits();
        $prices = [];
        foreach ($amounts as $key => $amount) {
            $precio_promedio_compra = $avgs[$amount['idMoneda']];
            if (empty($prices[$amount['book']])) {
                try {
                    $ticker = $_bitso->ticker(["book" => $amount['book']]);

                    if (!$ticker->payload->ask || !$ticker->payload->bid) {
                        throw new bitsoException('price zero: ' . $ticker->payload->book, HTTPStatusCodes::ServiceUnavailable);
                    }

                    $prices[$amount['book']] = round(($ticker->payload->ask + $ticker->payload->bid) / 2, 2);
                } catch (bitsoException $exception) {
                    $Precios_Monedas = new Precios_Monedas();
                    $fallback = $Precios_Monedas->selectFallbackPrice($amount);
                    $prices[$amount['book']] = (double)$fallback;
                    switch (true) {
                        case str_contains($exception->getMessage(), "Unknown OrderBook"):
                            break;
                        default:
                            $ticker_error = $exception->getMessage();
                            break;
                    }
                }
            }

            $precio = $prices[$amount['book']];
            $actual = $amount['cantidad'] * $precio;
            $limite_compra = $limits['buy'][$amount['idMoneda']]['threshold'] ?? 0;
            $limite_venta = $limits['sell'][$amount['idMoneda']]['threshold'] ?? 0;
            $limite_monto = $limits['sell'][$amount['idMoneda']]['amount'] ?? 0;
            $costo = $limite_venta ?: $amount['costo'];

            $porcentaje = $costo != 0 ? (($actual - $costo) / abs($costo)) : 0;
            $porcentaje = ($costo > 0) ? $porcentaje : (($precio && $precio_promedio_compra) ? $precio / $precio_promedio_compra : null);

            if ($limite_venta > 0 || $limite_compra > 0) {
                $costo = $limite_venta;
                $porcentaje = $costo != 0 ? (($actual - $costo) / abs($costo)) : 0;
                $porcentaje = ($costo > 0) ? $porcentaje : (($precio && $precio_promedio_compra) ? $precio / $precio_promedio_compra : null);
            }

            //if(old>0,(new/old-1),((new+abs(old)/abs(old))
            //$porcentaje = $costo > 0 ? ($actual / $costo - 1) : ($costo != 0 ? ($actual + abs($costo) / abs($costo)) : 0);

            $amounts[$key]['precio'] = $precio;
            $amounts[$key]['total'] = $actual;
            $amounts[$key]['porcentaje'] = $porcentaje;
            $amounts[$key]['promedio'] = ((float)$amount['cantidad'] && $costo > 0 ? $costo / $amount['cantidad'] : 0);

            $amounts[$key]['limite']['venta'] = $limite_venta;
            $amounts[$key]['limite']['monto'] = $limite_monto;
//            $amounts[$key]['costo'] = $limite_venta ?: $amount['costo'];

            $amounts[$key]['estadisticas'] = Trades::getTradesByCoin($user_id, $amount['idMoneda']);

            if (isset($ticker_error)) {
                Usuarios_Notificaciones::BITSO_ERROR($ticker_error);
                throw new CoreException("Bitso error: " . $ticker_error, 503);
            }

            if ($limite_venta > 0) {
                $limit_sell_percentage = $limite_monto / $limite_venta;
                if ($porcentaje >= $limit_sell_percentage) {
                    Usuarios_Notificaciones::LIMITE_VENTA($amount, abs($limit_sell_percentage), $porcentaje);
                }
            }
            if ($limite_compra > 0) {
                $limit_buy_percentage = $limite_monto / $limite_compra * -1;
                if ($porcentaje <= $limit_buy_percentage) {
                    Usuarios_Notificaciones::LIMITE_COMPRA($amount, abs($limit_buy_percentage), $porcentaje);
                }
            }

            if ($bitso->isKeySet()) {
                $balances = array_filter(array_column($bitso->selectBalances(), "total", "currency"), function ($balance) {
                    return $balance > 0;
                });

                $diff = $balances[$amount['idMoneda']] - $amount['cantidad'];
                if ($diff != 0) {
                    if ($amount['idMoneda'] === "mxn") {
                        $diff = round($diff, 2);
                        $costo = $diff;
                    } else {
                        try {
                            $ticker = $_bitso->ticker(['book' => $amount['book']])->payload;
                            $costo = abs($diff) * (($ticker->ask + $ticker->bid) / 2);
                        } catch (bitsoException $exception) {
                            $ticker_error = $exception->getMessage();
                        }
                    }
                    if (!isset($ticker_error) && $bitso->isKeySet()) {
                        if ($diff > 0 && $limite_venta !== null && $limite_venta < ($actual + $costo)) {
                            $o_usuarios_monedas_limites = new Usuarios_Monedas_Limites();
                            $o_usuarios_monedas_limites->updateLimit([
                                'id_usuario' => $user_id,
                                'id_moneda' => $amount['idMoneda'],
                                'limite' => $actual + $costo,
                            ]);
                        }
                        if ($costo != 0 || $diff != 0) {
                            $Usuarios_Transacciones->insertTrade($user_id, $amount['idMoneda'], $costo, $diff, $diff > 0);
                        }
                    }
                }
            }
        }

        return compact('amounts');
    }

    protected function setCoinLimit()
    {
        System::check_value_empty($_POST, ['user_token', 'idMoneda']);
        $user = System::decode_token($_POST['user_token']);
        $user_id = $user['id'];
        $user_id = System::decrypt($user_id);

        $o_usuarios_monedas_limites = new Usuarios_Monedas_Limites();
        $o_usuarios_monedas_limites->updateLimit([
            'id_usuario' => $user_id,
            'id_moneda' => $_POST['idMoneda'],
            'limite' => $_POST['limit'],
        ]);
    }
}
