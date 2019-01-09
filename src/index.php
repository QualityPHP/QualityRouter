<?php

require __DIR__."/../vendor/autoload.php";

use yak0d3\QualityRouter\QRouter;
use yak0d3\QualityRouter\Request;

$router = new QRouter('config.php');
$request = new Request($router);

$request->intercept();
