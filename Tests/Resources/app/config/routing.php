<?php

use Symfony\Component\Routing\RouteCollection;

$collection = new RouteCollection();
$collection->addCollection(
    $loader->import(__DIR__.'/../../../../Resources/config/routing.yml')
);

return $collection;
