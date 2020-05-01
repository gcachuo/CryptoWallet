<?php

use Controller\Users;

System::init(['DIR' => __DIR__ . '/../../../../api']);

['id_usuario' => $id_usuario, 'id_moneda' => $id_moneda, 'costo' => $costo, 'cantidad' => $cantidad] = $_POST;

$id_usuario = System::decrypt($id_usuario);

$Users = new Users();
$Users->addTrade($id_usuario, $id_moneda, $costo, $cantidad);

System::redirect('cartera');
