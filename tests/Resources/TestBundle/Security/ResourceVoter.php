<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Tests\Resources\TestBundle\Security;

use Symfony\Cmf\Bundle\ResourceRestBundle\Controller\ResourceController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ResourceVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [ResourceController::ROLE_RESOURCE_READ, ResourceController::ROLE_RESOURCE_WRITE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ('security' !== $subject['repository_name']) {
            return true;
        }

        if ('/tests/cmf/articles/public' !== substr($subject['path'], 0, 27)) {
            return false;
        }

        if (ResourceController::ROLE_RESOURCE_WRITE === $attribute) {
            return false === strpos($subject['path'], 'admin');
        }

        return true;
    }
}
