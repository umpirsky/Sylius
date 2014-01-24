<?php

namespace Hypebeast\Bundle\WebBundle\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\WebBundle\Menu\MenuBuilder;
use Symfony\Component\HttpFoundation\Request;

class FrontendMenuBuilder extends MenuBuilder
{
    /**
     * Creates user account menu
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    public function createAccountMenu()
    {
        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => ['class' => 'list-unstyled']
        ]);

        $child = $menu->addChild($this->translate('sylius.account.title'), array(
            'childrenAttributes' => array('class' => 'list-unstyled'),
            'labelAttributes'    => array('class' => 'header'),
            'extras' => ['safe_label' => true]
        ));

        $child->addChild('profile', array(
            'route' => 'fos_user_profile_edit',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.profile')),
        ))->setLabel($this->translate('sylius.frontend.menu.account.profile'));

//        $child->addChild('password', array(
//            'route' => 'fos_user_change_password',
//            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.password')),
//            'labelAttributes' => array('icon' => 'icon-lock', 'iconOnly' => false)
//        ))->setLabel($this->translate('sylius.frontend.menu.account.password'));

        $child->addChild('orders', array(
            'route' => 'sylius_account_order_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.orders')),
        ))->setLabel($this->translate('sylius.frontend.menu.account.orders'));

        $child->addChild('addresses', array(
            'route' => 'sylius_account_address_index',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.account.addresses')),
            'labelAttributes' => array('icon' => 'icon-envelope', 'iconOnly' => false)
        ))->setLabel($this->translate('sylius.frontend.menu.account.addresses'));

        return $menu;
    }
} 