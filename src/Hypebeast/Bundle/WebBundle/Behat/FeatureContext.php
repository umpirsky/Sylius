<?php

namespace Hypebeast\Bundle\WebBundle\Behat;

use Sylius\Bundle\WebBundle\Behat\FeatureContext as BaseContext;

class FeatureContext extends BaseContext
{
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);

        $this->useContext('web-user', new WebUser());
    }
}
