<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
