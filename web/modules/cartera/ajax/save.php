<?php

use Controller\Users;

System::init(['DIR' => __DIR__ . '/../../../../api']);

['id_usuario' => $id_usuario, 'id_moneda' => $id_moneda, 'costo' => $costo, 'cantidad' => $cantidad, 'tipo' => $tipo] = $_POST;

$id_usuario = System::decrypt($id_usuario);

$Users = new Users();
$Users->addTrade($id_usuario, $id_moneda, $costo, $cantidad, $tipo);

System::redirect('cartera');
