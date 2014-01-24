<?php

namespace Hypebeast\Bundle\WebBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;

class HypebeastExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sort_facet_terms', array($this, 'sortFacetTerms')),
        ];
    }

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

    public function sortFacetTerms($facets, $type)
    {
        switch ($type) {
            case 'size':
                $sample = ['Free Size','XS','S','M','L','XL','XXL','28','29','30','31','32','33','34','35','36', '40',
                    '42','US 7.5','US 8','US 8.5','US 9','US 9.5','US 10','US 10.5','US 11','US 11.5','US 12','JP 25.5',
                    'JP 26.5','JP 27.5','JP 28.5','JP 29.5','EU 40','EU 41','EU 42','EU 43','EU 44','EU 45','EU 46','7',
                    '7 1/8','7 1/4','7 3/8','7 1/2','7 5/8','7 3/4','7 7/8','8'];

                return $this->sortFacetTermsBySample($facets, $sample);
                break;
            default:
                return $this->sortFacetTermsByName($facets);
        }
    }

    private function sortFacetTermsByName($facets)
    {
        usort($facets, function ($a, $b) {
            return ($a['term'] < $b['term']) ? -1 : 1;
        });

        return $facets;
    }

    private function sortFacetTermsBySample($facets, $sample)
    {
        $ordered = array();

        foreach ($sample as $term) {
            foreach ($facets as $i => $facet) {
                if ($facet['term'] == $term) {
                    $ordered[] = $facet;
                    unset($facets[$i]);
                    break;
                }
            }
        }

        return $ordered + $facets;
    }

    public function getName()
    {
        return 'hypebeast';
    }
}
