<?php

namespace Hypebeast\Bundle\WebBundle\Security;

use Sylius\Bundle\CoreBundle\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class HypebeastToken extends AbstractToken
{
    private $id;
    private $username;

    public function __construct($id, $username, User $user = null)
    {
        $this->id       = $id;
        $this->username = $username;

        if (null !== $user) {
            parent::__construct($user->getRoles());

            $this->id       = $user->getId();
            $this->username = $user->getUsername();

            $this->setUser($user);
            $this->setAuthenticated($user);
        } else {
            parent::__construct([]);
        }
    }

    public function getId()
    {
        return $this->id;
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
