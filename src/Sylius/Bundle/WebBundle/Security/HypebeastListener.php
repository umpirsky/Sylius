<?php

namespace Sylius\Bundle\WebBundle\Security;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class HypebeastListener extends ListenerInterface
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

        if (false === $this->supports($request)) {

            return;
        }

        list($api_key, $username) = $this->getCredentials($request);

        if ($api_key !== $apiKey) {

            return;
        }
    }

    protected function supports(Request $request)
    {
        return $request->query->has('api_key') && $request->query->has('username');
    }

    protected function getCredentials(Request $request)
    {
        return [
            base64_decode($request->query->get('api_key')),
            base64_decode($request->query->get('username')),
        ];
    }
}
