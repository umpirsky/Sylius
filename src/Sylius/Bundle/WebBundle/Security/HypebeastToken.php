<?php

namespace Sylius\Bundle\WebBundle\Security;

use Sylius\Bundle\CoreBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class HypebeastToken extends AbstractToken
{
    public function __construct($username, User $user)
    {
        $this->username = $username;

        if (null !== $user) {
            parent::__construct($user->getRoles());

            $this->setUser($user);
            $this->setAuthenticated($user);
        }
    }

    public function getCredentials()
    {
        return $this->username;
    }
}
