<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Fixtures\App\Description;

use Symfony\Cmf\Component\Resource\Description\Description;
use Symfony\Cmf\Component\Resource\Description\DescriptionEnhancerInterface;
use Symfony\Cmf\Component\Resource\Puli\Api\PuliResource;
use Symfony\Cmf\Component\Resource\Repository\Resource\CmfResource;

class DummyEnhancer implements DescriptionEnhancerInterface
{
    public function enhance(Description $description)
    {
        $description->set('name_reverse', strrev($description->getResource()->getName()));
    }

    public function supports(PuliResource $resource)
    {
        return $resource instanceof CmfResource;
    }
}
