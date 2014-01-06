<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethod as BaseShippingMethod;

/**
 * Shipping method available for selected zone.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
{
    /**
     * Geographical zone.
     *
     * @var ZoneInterface
     */
    protected $zone;

    protected $estimationDaysFrom = 0;

    protected $estimationDaysTo = 0;

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(ZoneInterface $zone)
    {
        $this->zone = $zone;

        return $this;
    }

    public function getEstimationDaysFrom()
    {
        return $this->estimationDaysFrom;
    }

    public function setEstimationDaysFrom($estimationDaysFrom)
    {
        $this->estimationDaysFrom = $estimationDaysFrom;

        return $this;
    }

    public function getEstimationDaysTo()
    {
        return $this->estimationDaysTo;
    }

    public function setEstimationDaysTo($estimationDaysTo)
    {
        $this->estimationDaysTo = $estimationDaysTo;

        return $this;
    }
}
