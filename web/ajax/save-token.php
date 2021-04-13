<?php
include __DIR__ . "/../core/System.php";
System::init(['DIR' => __DIR__.'/../', 'ENV' => 'www']);

$headers = apache_request_headers();
$authorization = $headers['Authorization'];
$token = str_replace('Bearer ', '', $authorization);

session_start();
$_SESSION['user_token'] = $token;
session_write_close();

JsonResponse::sendResponse('Completed.');
