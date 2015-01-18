<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Helper;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Bundle\ResourceRestBundle\Helper\PathHelper;

class PathHelperTest extends ProphecyTestCase
{
    private $helper;

    public function setUp()
    {
        parent::setUp();

        $this->helper = new PathHelper();
    }

    public function provideRelativize()
    {
        return array(
            array('/foo/bar', 'foo/bar'),
            array('foo/bar', 'foo/bar'),
        );
    }

    /**
     * @dataProvider provideRelativize
     */
    public function testRelativie($subject, $expectedResult)
    {
        $res = $this->helper->relativize($subject);
        $this->assertEquals($res, $expectedResult);
    }
}
