<?php

namespace Sylius\Bundle\WebBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class HypebeastProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if (null === $user) {
            throw new BadCredentialsException(sprintf(
                'The key "%s" does not match any user.',
                $token->getKey()
            ));
        }

        return new HypebeastToken($token->getUsername(), $user);
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof HypebeastToken;
    }
}
