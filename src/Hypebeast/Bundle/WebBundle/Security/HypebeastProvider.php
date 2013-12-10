<?php

namespace Hypebeast\Bundle\WebBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sylius\Bundle\CoreBundle\Model\User;

class HypebeastProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            $user = (new User);
            $user->setId($token->getId());
            $user->setUsername($token->getUsername());
            $user->setPassword(md5(uniqid()));
        }

        return new HypebeastToken($token->getId(), $token->getUsername(), $user);
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof HypebeastToken;
    }
}
