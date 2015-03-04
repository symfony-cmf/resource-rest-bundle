#!/bin/bash

echo "<?php exit(defined('HHVM_VERSION') ? 1 : 0);" > /tmp/ishhvm.php

php /tmp/ishhvm.php

if [[ $? == 1 ]]; then
    echo "HHVM detected - skipping tests that require the built-in webserver"
else
    echo "PHP detected"
    ./vendor/symfony-cmf/testing/bin/server &
    ./vendor/bin/behat
fi
