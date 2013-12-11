<?php

namespace Hypebeast\Bundle\WebBundle\Behat;

use Sylius\Bundle\WebBundle\Behat\WebUser as BaseContext;

class WebUser extends BaseContext
{
    public function __construct()
    {
        parent::__construct();

        $this->useContext('data', new DataContext());
    }
}
