<?php

namespace Sylius\Bundle\UmpirskyBundle\Entity;

use Sylius\Component\Product\Model\Attribute as BaseAttribute;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_attribute")
 */
class Attribute extends BaseAttribute
{
    protected function getTranslationEntityClass()
    {
        return get_parent_class($this).'Translation';
    }
}
