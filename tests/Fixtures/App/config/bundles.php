<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebServerBundle\WebServerBundle;
use Symfony\Cmf\Bundle\ResourceBundle\CmfResourceBundle;
use Symfony\Cmf\Bundle\ResourceRestBundle\CmfResourceRestBundle;

return [
    CmfResourceRestBundle::class => ['all' => true],
    CmfResourceBundle::class => ['all' => true],
    JMSSerializerBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    WebServerBundle::class => ['all' => true],
];
