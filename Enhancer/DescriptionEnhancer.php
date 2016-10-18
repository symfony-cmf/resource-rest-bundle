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

use Puli\Repository\Api\Resource\PuliResource;
use Symfony\Cmf\Component\Resource\Description\DescriptionFactory;

/**
 * Serialize the descriptions.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class DescriptionEnhancer implements EnhancerInterface
{
    /**
     * @var DescriptionEnhancer
     */
    private $descriptionFactory;

    public function __construct(DescriptionFactory $descriptionFactory)
    {
        $this->descriptionFactory = $descriptionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function enhance(array $data, PuliResource $resource)
    {
        $data['description'] = $this->descriptionFactory->getPayloadDescriptionFor($resource)->all();

        return $data;
    }
}
