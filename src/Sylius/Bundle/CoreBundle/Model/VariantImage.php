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

class VariantImage extends Image implements VariantImageInterface
{
    protected $variant;
    protected $position;
    protected $model = false;

    public function getVariant()
    {
        return $this->variant;
    }

    public function setVariant(VariantInterface $variant = null)
    {
        $this->variant = $variant;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
    public function isModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }
}
