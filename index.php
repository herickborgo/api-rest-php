<?php

header('Content-Type: application/json; charset=utf-8;');
header('Accept: application/json;');
date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . DIRECTORY_SEPARATOR .'autoload.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'router.php';

use Config\Database\Connection;

$connection = Connection::connect();
$connection->migrate();
