<?php

require __DIR__."/../vendor/autoload.php";

use QualityPHP\QualityRouter\QRouter;

$dotenv = Dotenv\Dotenv::create(__DIR__);
$config = $dotenv->load();

$router = new QRouter($config);
