<?php

namespace QualityPHP\QualityRouter;

function pointTo(string $filename)
{
    include $filename;
}

QRouter::POST('/', function() {
    echo "Test";
});

QRouter::GET('/hello', function () {
    echo "here you go";
});