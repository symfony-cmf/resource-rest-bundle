<?php

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Resources\TestBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;

/**
 * @PHPCR\Document()
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
