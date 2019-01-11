<?php

require __DIR__."/../vendor/autoload.php";

use QualityPHP\QualityRouter\QRouter;
use QualityPHP\QualityRouter\Request;

$router = new QRouter('config.php');
$request = new Request($router, $_SERVER);

$request->intercept();
