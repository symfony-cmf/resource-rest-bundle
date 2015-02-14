<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Enhancer;

use JMS\Serializer\Context;
use Puli\Repository\Api\Resource\Resource;
use Sonata\AdminBundle\Admin\Pool;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Add links and metainfo from Sonata Admin
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class SonataAdminEnhancer implements EnhancerInterface
{
    private $pool;
    private $urlGenerator;

    public function __construct(Pool $pool, UrlGeneratorInterface $urlGenerator)
    {
        $this->pool = $pool;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function enhance(Context $context, Resource $resource)
    {
        $object = $resource->getPayload();

        // sonata has dependency on ClassUtils so this is fine.
        $class = ClassUtils::getClass($object);

        if (false === $this->pool->hasAdminByClass($class)) {
            return;
        }

        $admin = $this->pool->getAdminByClass($class);
        $visitor = $context->getVisitor();

        $links = array();
        foreach (array_keys($admin->getRoutes()) as $routeName) {
            $url = $this->urlGenerator->generate($routeName, array(
                $admin->getIdParameter(),
                $admin->getUrlsafeIdentifier($object)
            ));

            $links[$routeName] = $url;
        }

        $visitor->addData('_admin_label', $admin->getLabel());
        $visitor->addData('_admin_links', $links);
    }
}

