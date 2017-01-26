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

use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;

/**
 * Serialize the payload.
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PayloadEnhancer implements EnhancerInterface
{
    /**
     * {@inheritdoc}
     */
    public function enhance(array $data, PuliResource $resource)
    {
        $payload = $resource->getPayload();
        $data['payload'] = $payload;

        return $data;
    }
}
