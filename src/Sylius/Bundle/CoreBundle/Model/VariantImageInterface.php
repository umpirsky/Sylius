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

interface VariantImageInterface extends ImageInterface
{
    public function getVariant();
    public function setVariant(VariantInterface $variant = null);
    public function getPosition();
    public function setPosition($position);
    public function isModel();
    public function setModel($model);
}
