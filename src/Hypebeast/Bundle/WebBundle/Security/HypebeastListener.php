<?php

namespace Hypebeast\Bundle\WebBundle\Security;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;

class HypebeastListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $apiKey;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $apiKey)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->apiKey = $apiKey;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $token = $this->loadTokenFromRequest($request);

        if (null === $token) {
            $token = $this->loadTokenFromSession($request->getSession());
        }

        if (null === $token) {

            return;
        }

        $token = $this->authenticationManager->authenticate($token);
        $this->securityContext->setToken($token);
    }

    protected function supports(Request $request)
    {
        return $request->query->has('api_key')
            && $request->request->has('username')
            && $request->request->has('email')
        ;
    }

    protected function getCredentials(Request $request)
    {
        return [
            base64_decode($request->query->get('api_key')),
            $request->request->get('username'),
            $request->request->get('email'),
        ];
    }

    protected function loadTokenFromSession(Session $session)
    {
        if ($session->has('_security_hypebeast')) {

            $token = unserialize($session->get('_security_hypebeast'));

            if ($token instanceof HypebeastToken) {

                return new HypebeastToken($token->getUsername(), $token->getUser());
            }
        }
    }

    protected function loadTokenFromRequest(Request $request)
    {
        if (false === $this->supports($request)) {

            return;
        }

        list($api_key, $username, $email) = $this->getCredentials($request);

        if ($api_key !== $this->apiKey) {

            return;
        }

        return new HypebeastToken($username);
    }
}
