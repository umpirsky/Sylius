<?php

namespace Hypebeast\Bundle\WebBundle\Menu;

use Sylius\Bundle\WebBundle\Menu\BackendMenuBuilder as BaseBackendMenuBuilder;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

class BackendMenuBuilder extends BaseBackendMenuBuilder
{
    public function createSidebarMenu(Request $request)
    {
        $menu = parent::createSidebarMenu($request);

        $childOptions = array(
            'attributes'         => array(),
            'childrenAttributes' => array('class' => 'nav'),
            'labelAttributes'    => array('class' => 'nav-header')
        );

        $this->addBannerMenu($menu, $childOptions, 'main');

        return $menu;
    }

    public function addBannerMenu(ItemInterface $menu, array $childOptions, $section)
    {
        $child = $menu
            ->addChild('banners', $childOptions)
            ->setLabel('Banners')
        ;

        $child->addChild('homepage', array(
            'route' => 'hypebeast_homepage_banner_settings',
            'labelAttributes' => array('icon' => 'glyphicon glyphicon-cog'),
        ))->setLabel('Hompage Banner Settings');
    }


}
