<?php

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    public function authenticateAction()
    {
        return new Response(
            null,
            null === $this->getUser() ? 404 : 200
        );
    }
}
