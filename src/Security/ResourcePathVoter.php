<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Security;

use Symfony\Cmf\Bundle\ResourceRestBundle\Controller\ResourceController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
class ResourcePathVoter extends Voter
{
    private $accessDecisionManager;
    private $accessMap;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager, array $accessMap)
    {
        $this->accessDecisionManager = $accessDecisionManager;
        $this->accessMap = $accessMap;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [ResourceController::ROLE_RESOURCE_READ, ResourceController::ROLE_RESOURCE_WRITE])
            && is_array($subject) && isset($subject['repository_name']) && isset($subject['path']);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        foreach ($this->accessMap as $rule) {
            if (!$this->ruleMatches($rule, $attribute, $subject)) {
                continue;
            }

            if ($this->accessDecisionManager->decide($token, $rule['require'])) {
                return true;
            }
        }

        return false;
    }

    private function ruleMatches($rule, $attribute, $subject)
    {
        if (!in_array($attribute, $rule['attributes'])) {
            return false;
        }

        if (null !== $rule['repository'] && $rule['repository'] !== $subject['repository_name']) {
            return false;
        }

        if (!preg_match('{'.$rule['pattern'].'}', $subject['path'])) {
            return false;
        }

        return true;
    }
}
