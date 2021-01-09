<?php
include __DIR__."/../core/System.php";
$headers = apache_request_headers();
$authorization=$headers['Authorization'];
$token=str_replace('Bearer ','',$authorization);

System::sessionSet('user_token',$token);
