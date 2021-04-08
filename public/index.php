<?php

use HerickBorgo\RestApi\Application;

header('Content-Type: application/json; charset=utf-8;');
header('Accept: application/json;');
date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'helper.php';

Application::run();
