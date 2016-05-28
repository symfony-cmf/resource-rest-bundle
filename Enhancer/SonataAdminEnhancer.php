<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Enhancer;

use Sonata\AdminBundle\Admin\Pool;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Puli\Repository\Api\Resource\PuliResource;

/**
 * Add links and meta-info from Sonata Admin.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SonataAdminEnhancer implements EnhancerInterface
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * __construct.
     *
     * @param Pool                  $pool
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(Pool $pool, UrlGeneratorInterface $urlGenerator)
    {
        $this->pool = $pool;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function enhance(array $data, PuliResource $resource)
    {
        $object = $resource->getPayload();

        // sonata has dependency on ClassUtils so this is fine.
        $class = ClassUtils::getClass($object);

        if (false === $this->pool->hasAdminByClass($class)) {
            return $data;
        }

        $admin = $this->pool->getAdminByClass($class);

        $links = array();

        $routeCollection = $admin->getRoutes();

        foreach ($routeCollection->getElements() as $code => $route) {
            $routeName = $route->getDefault('_sonata_name');
            $url = $this->urlGenerator->generate($routeName, array(
                $admin->getIdParameter() => $admin->getUrlsafeIdentifier($object),
            ), true);

            $routeRole = substr($code, strlen($admin->getCode()) + 1);

            $links[$routeRole] = $url;
        }

        $data['label'] = $admin->toString($object);
        $data['sonata_label'] = $admin->getLabel();
        $data['sonata_links'] = $links;

        return $data;
    }
}
