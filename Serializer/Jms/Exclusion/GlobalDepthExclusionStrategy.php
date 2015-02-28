<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\Exclusion;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;

class GlobalDepthExclusionStrategy implements ExclusionStrategyInterface
{
    private $globalMaxDepth;

    /**
     * @param integer $globalMaxDepth
     */
    public function __construct($globalMaxDepth = null)
    {
        $this->globalMaxDepth = $globalMaxDepth;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $context)
    {
        return $this->isTooDeep($context);
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $context)
    {
        return $this->isTooDeep($context);
    }

    private function isTooDeep(Context $context)
    {
        $depth = $context->getDepth();
        $metadataStack = $context->getMetadataStack();

        $nthProperty = 0;
        // iterate from the first added items to the lasts
        for ($i = $metadataStack->count() - 1; $i > 0; $i--) {
            $metadata = $metadataStack[$i];
            if ($metadata instanceof PropertyMetadata) {
                $nthProperty++;
                $relativeDepth = $depth - $nthProperty;

                if (null !== $metadata->maxDepth && $relativeDepth > $metadata->maxDepth) {
                    return true;
                }

                if (null !== $this->globalMaxDepth && $relativeDepth > $this->globalMaxDepth) {
                    return true;
                }
            }
        }

        return false;
    }
}
