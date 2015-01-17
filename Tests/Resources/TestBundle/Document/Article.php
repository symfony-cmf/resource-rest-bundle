<?php

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Resources\TestBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @PHPCR\Document()
 * @Hateoas\Relation("self", href="/path/to/this")
 */
class Article
{
    /**
     * @PHPCR\Id()
     */
    public $id;

    /**
     * @PHPCR\String()
     */
    public $title;

    /**
     * @PHPCR\String()
     */
    public $body;
}
