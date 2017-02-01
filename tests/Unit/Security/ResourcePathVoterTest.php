<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Security;

use Symfony\Cmf\Bundle\ResourceRestBundle\Security\ResourcePathVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as V;

class ResourcePathVoterTest extends \PHPUnit_Framework_TestCase
{
    private $accessDecisionManager;

    protected function setUp()
    {
        $this->accessDecisionManager = $this->prophesize(AccessDecisionManagerInterface::class);
    }

    /**
     * @dataProvider provideVoteData
     */
    public function testVote($rules, $subject, array $attributes, $result)
    {
        $token = $this->prophesize(TokenInterface::class)->reveal();

        $this->accessDecisionManager->decide($token, ['ROLE_USER'])->willReturn(true);
        $this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])->willReturn(false);

        $voter = new ResourcePathVoter($this->accessDecisionManager->reveal(), $rules);

        $this->assertSame($result, $voter->vote($token, $subject, $attributes));
    }

    public function provideVoteData()
    {
        $ruleSet1 = [
            $this->buildRule('^/', ['ROLE_USER'], ['CMF_RESOURCE_READ']),
            $this->buildRule('^/cms/private', ['ROLE_ADMIN'], ['CMF_RESOURCE_WRITE']),
        ];

        return [
            // Basic behaviour
            [[$this->buildRule('^/')], $this->buildSubject('/cms/articles/foo'), ['CMF_RESOURCE_READ'], V::ACCESS_GRANTED],
            [[$this->buildRule('^/')], $this->buildSubject('/cms/articles/foo'), ['CMF_RESOURCE_WRITE'], V::ACCESS_GRANTED],
            [[$this->buildRule('^/', ['ROLE_ADMIN'])], $this->buildSubject('/cms/articles/foo'), ['CMF_RESOURCE_READ'], V::ACCESS_DENIED],

            // Multiple rules
            [$ruleSet1, $this->buildSubject('/cms/private/admin'), ['CMF_RESOURCE_READ'], V::ACCESS_GRANTED],
            [$ruleSet1, $this->buildSubject('/cms/private/admin'), ['CMF_RESOURCE_WRITE'], V::ACCESS_DENIED],
            [$ruleSet1, $this->buildSubject('/cms/public'), ['CMF_RESOURCE_READ', 'CMF_RESOURCE_WRITE'], V::ACCESS_GRANTED],

            // Unsupported attributes or subjects
            [[], $this->buildSubject('/cms/articles'), ['CMF_RESOURCE_READ'], V::ACCESS_DENIED],
            [[$this->buildRule('^/')], $this->buildSubject('/cms/articles'), ['ROLE_USER'], V::ACCESS_ABSTAIN],
            [[$this->buildRule('^/')], new \stdClass(), ['CMF_RESOURCE_READ'], V::ACCESS_ABSTAIN],

            // Repository name matching
            [[$this->buildRule('^/')], $this->buildSubject('/cms/articles', 'other_repo'), ['CMF_RESOURCE_READ'], V::ACCESS_DENIED],
            [[$this->buildRule('^/', ['ROLE_USER'], ['CMF_RESOURCE_READ'], 'other_repo')], $this->buildSubject('/cms/articles'), ['CMF_RESOURCE_READ'], V::ACCESS_DENIED],
        ];
    }

    private function buildRule($pattern, $require = ['ROLE_USER'], $attributes = ['CMF_RESOURCE_READ', 'CMF_RESOURCE_WRITE'], $repository = 'default')
    {
        return ['pattern' => $pattern, 'attributes' => $attributes, 'require' => $require, 'repository' => $repository];
    }

    private function buildSubject($path, $repository = 'default')
    {
        return ['path' => $path, 'repository_name' => $repository];
    }
}
