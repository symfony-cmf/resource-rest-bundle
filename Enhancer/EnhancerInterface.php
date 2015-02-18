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

/**
 * Enhancer classes enhance the REST response for resources
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
interface EnhancerInterface
{
    /**
     * Enhance the given serialization context.
     *
     * For example:
     *
     *     $context->addData('foobar', 'Some value');
     *
     * @param Context Serialization context
     * @param Resource The resource being serialized
     */
    public function enhance(array $data, Context $context, Resource $resource);
}
