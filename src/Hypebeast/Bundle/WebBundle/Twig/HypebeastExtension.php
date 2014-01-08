<?php

namespace Hypebeast\Bundle\WebBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Function_Method;

class HypebeastExtension extends Twig_Extension
{
    public function getFilters()
    {
        return [
            'push' => new Twig_Filter_Method($this, 'push'),
        ];
    }

    public function push($array, $key, $value = null)
    {
        if (null === $value) {
            $array[] = $key;
        } else {
            $array[$key] = $value;
        }

        return $array;
    }

    public function getName()
    {
        return 'hypebeast';
    }
}
