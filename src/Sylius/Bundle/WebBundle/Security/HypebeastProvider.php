<?php

namespace Sylius\Bundle\WebBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

class HypebeastProvider implements AuthenticationProviderInterface
{
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->findUser($token->getUsername());

        if (null === $user) {
            throw new BadCredentialsException(sprintf(
                'The live API key "%s" does not match any user.',
                $token->getKey()
            ));
        }

        return new LiveApiKeyToken($token->getKey(), $user);
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof HypebeastToken;
    }

    public function findUser($username)
    {
        return $this
            ->doctrine
            ->getRepository('Sylius\Bundle:Core:User')
            ->findOneBy(['username' => $username])
        ;
    }
}
