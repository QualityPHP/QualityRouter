<?php

require "../boot.php";

use QualityPHP\QualityRouter\Request;

$request = new Request($router, $_SERVER);

$request->intercept();