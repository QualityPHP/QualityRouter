<?php

namespace yak0D3\QualityRouter;

QRouter::POST('/', function () {
    echo "hello";
});

QRouter::GET('/hello', function () {
    echo "here you go";
});
