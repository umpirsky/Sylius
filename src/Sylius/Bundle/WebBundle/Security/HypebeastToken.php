<?php

namespace Sylius\Bundle\WebBundle\Security;

use Sylius\Bundle\CoreBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class HypebeastToken extends AbstractToken
{
    private $username;

    public function __construct($username, User $user = null)
    {
        $this->username = $username;

        if (null !== $user) {
            parent::__construct($user->getRoles());

            $this->setUser($user);
            $this->setAuthenticated($user);
            $this->username = $user->getUsername();
        } else {
            parent::__construct([]);
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getCredentials()
    {
        return $this->username;
    }
}
