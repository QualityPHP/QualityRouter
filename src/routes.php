<?php

namespace QualityPHP\QualityRouter;

// QRouter::CONFSET('AUTO', true); # This will make the router ignnore all routes below
                                   # and respond with file contents from the app dir (provided in the config.php file)
                                   # e.g. Request uri = /FILENAME, then the response will be /APP_DIR/FILENAME.php

QRouter::GET('/get', function () {
    echo "GET";
});

QRouter::POST('/post', function () {
    echo "POST";
});

QRouter::ANY('/any', function() {
    echo "ANY";
});