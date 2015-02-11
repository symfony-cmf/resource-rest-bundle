<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Decorator;

use JMS\Serializer\Context;
use Puli\Repository\Api\Resource\Resource;

/**
 * Decorator classes decorate the REST response for resources
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface DecoratorInterface
{
    /**
     * Decorate the given serialization context.
     *
     * For example:
     *
     *     $context->addData('foobar', 'Some value');
     *
     * @param Context Serialization context
     * @param resource The resource being serialized
     */
    public function decorate(Context $context, Resource $resource);
}
