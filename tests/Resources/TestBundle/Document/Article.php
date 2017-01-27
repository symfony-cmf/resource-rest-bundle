<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @PHPCR\Field(type="string")
     */
    public $title;

    /**
     * @PHPCR\Field(type="string")
     */
    public $body;
}
