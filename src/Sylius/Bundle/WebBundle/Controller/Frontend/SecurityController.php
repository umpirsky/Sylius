<?php

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    public function authenticateAction(Request $request)
    {
        if (null !== $user = $this->getUser()) {

            if (null !== $user->getId()) {
                $user = $this->get('sylius.repository.user')->find($user->getId());
            }

            foreach ($request->request as $field => $value) {
                $method = sprintf('set%s', ucfirst($field));

                if (method_exists($user, $method)) {
                    $user->{$method}($value);
                }
            }

            $this->get('fos_user.user_manager')->updateUser($user);
        }

        return new Response(
            null,
            null === $user ? 404 : 200
        );
    }
}
