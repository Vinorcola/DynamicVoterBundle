<?php

namespace Vinorcola\DynamicVoterBundle\Security;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class DynamicVoter extends Voter
{
    /**
     * @var array
     */
    private $authorizationRules;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * DynamicVoter constructor.
     *
     * @param array                       $authorizationRules
     * @param CacheItemPoolInterface|null $cache
     */
    public function __construct(array $authorizationRules, CacheItemPoolInterface $cache = null)
    {
        $this->authorizationRules = $authorizationRules;
        $this->expressionLanguage = new ExpressionLanguage($cache);
        $this->expressionLanguage->register(
            'user_has_role',
            function($role) {
                return
                    'if (!($arguments[\'user\'] instanceof \Symfony\Component\Security\Core\User\UserInterface)) return false; ' .
                    'foreach ($arguments[\'user\']->getRoles() as $grantedRole) { ' .
                    'if ($grantedRole instance of \Symfony\Component\Security\Core\Role\Role) $grantedRole = $grantedRole->getRole(); ' .
                    'if ($grantedRole === \'' . $role . '\') return true; ' .
                    '} return false;';
            },
            function ($arguments, $role) {
                if (!($arguments['user'] instanceof UserInterface)) {
                    return false;
                }
                foreach ($arguments['user']->getRoles() as $grantedRole) {
                    if ($grantedRole instanceof Role) {
                        $grantedRole = $grantedRole->getRole();
                    }
                    if ($grantedRole === $role) {
                        return true;
                    }
                }

                return false;
            }
        );
    }

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return key_exists($attribute, $this->authorizationRules);
    }

    /**
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return $this->expressionLanguage->evaluate(
            $this->authorizationRules[$attribute],
            [
                'subject' => $subject,
                'user'    => $token->getUser(),
            ]
        );
    }
}
