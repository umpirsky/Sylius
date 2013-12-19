<?php

namespace Hypebeast\Bundle\WebBundle\Menu;

use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension;
use Sylius\Bundle\WebBundle\Menu\MenuBuilder;

class StoreMenuBuilder extends MenuBuilder
{
    public function createUserMenu(CartProviderInterface $cartProvider, SyliusMoneyExtension $moneyExtension)
    {
        $menu = $this->factory->createItem('user', [
            'childrenAttributes' => [
                'id' => 'site-account-menu'
            ],
        ]);
        $cart = $cartProvider->getCart();

        $menu->addChild('location', [
            'uri' => '#',
            'labelAttributes' => [
                'icon' => 'glyphicon glyphicon-globe'
            ],
        ])->setLabel('HK');

        $menu->addChild('cart', array(
            'route' => 'sylius_cart_summary',
            'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.cart', array(
                    '%items%' => $cart->getTotalItems(),
                    '%total%' => $moneyExtension->formatPrice($cart->getTotal())
                ))),
            'labelAttributes' => array('icon' => 'icon-shopping-cart icon-large')
        ))->setLabel($this->translate('%items% Items', array(
            '%items%' => $cart->getTotalItems(),
            '%total%' => $moneyExtension->formatPrice($cart->getTotal())
        )));

        if ($this->securityContext->isGranted('ROLE_USER')) {
            $account = $this->factory->createItem('account', [
                'route' => 'sylius_account_homepage',
                'attributes' => [
                    'class' => 'dropdown'
                ],
                'linkAttributes' => [
                    'class' => 'dropdown-toggle'
                ],
                'childrenAttributes' => [
                    'class' => 'account dropdown-menu pull-right'
                ]
            ])->setLabel($this->securityContext->getToken()->getUser()->getFirstName());

            $account->addChild('account', array(
                'route' => 'sylius_account_homepage',
            ))->setLabel($this->translate('sylius.frontend.menu.main.account'));

            if ($this->securityContext->isGranted('ROLE_SYLIUS_ADMIN')) {
                $account->addChild('administration', array(
                    'route' => 'sylius_backend_dashboard',
                ))->setLabel($this->translate('sylius.frontend.menu.main.administration'));
            }

            $account->addChild('logout', array(
                'route' => 'fos_user_security_logout',
            ))->setLabel($this->translate('sylius.frontend.menu.main.logout'));

            $menu->addChild($account);
        } else {
            $menu->addChild('login', array(
                'route' => 'fos_user_security_login',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.login')),
                'labelAttributes' => array('icon' => 'icon-lock icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.login'));
            $menu->addChild('register', array(
                'route' => 'fos_user_registration_register',
                'linkAttributes' => array('title' => $this->translate('sylius.frontend.menu.main.register')),
                'labelAttributes' => array('icon' => 'icon-user icon-large', 'iconOnly' => false)
            ))->setLabel($this->translate('sylius.frontend.menu.main.register'));
        }

        return $menu;
    }

    public function createSubNavbarMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('home', [
            'route' => 'sylius_homepage'
        ])->setLabel('Home');
        $menu->addChild('new_arrivals', [
            'route' => 'sylius_product_index_by_new_arrivals'
        ])->setLabel('New Arrivals');
        $menu->addChild('back', [
            'route' => 'sylius_product_index_by_back_in_stock'
        ])->setLabel('Back In Stock');
        $menu->addChild($this->createBrandsMenu())->setLabel('Brands');
        $menu->addChild($this->createClothingMenu())->setLabel('Clothing');
        $menu->addChild('footwear', [
            'uri' => '#'
        ])->setLabel('Footwear');
        $menu->addChild($this->createAccessoriesMenu())->setLabel('Accessories');
        $menu->addChild('print', [
            'uri' => '#'
        ])->setLabel('Print');
        $menu->addChild('gift_cards', [
            'uri' => '#'
        ])->setLabel('Gift Cards');
        $menu->addChild($this->createSaleMenu())->setLabel('Sale');

        return $menu;
    }

    public function createBrandsMenu()
    {
        $menu = $this->factory->createItem('brand', [
            'uri' => '#',
            'linkAttributes' => [
                'data-target' => '#site-subnavbar-dropdown-brands'
            ]
        ]);

        $menu->addChild('all', [
            'uri' => '#'
        ])->setLabel('View All Brands');

        $brands = [
            '10-deep' => '10.Deep',
            'black-scale' => 'Black Scale',
            'bwgh' => 'BWGH',
            'carhartt-work-in-progress' => 'Carhartt WORK IN PROGRESS',
            'clot' => 'CLOT',
            'herschel-supply-co' => 'Herschel Supp',
            'huf' => 'HUF',
            'i-love-ugly' => 'i love ugly.',
            'just-don' => 'Just Don',
            'les-artists' => 'LES (ART)ISTS',
            'maison-kitsune' => 'Maison Kitsune',
            'mister' => 'Mister',
            'naked-and-famous' => 'Naked &a',
            'only' => 'ONLY',
            'opening-ceremony' => 'Opening Ce',
            'stampd' => 'Stampd',
            'staple' => 'Staple',
            'stussy' => 'Stussy',
            'undefeated' => 'Undefeated',
        ];

        foreach ($brands as $slug => $brand) {
            $menu->addChild($slug, [
                'uri' => "#$slug"
            ])->setLabel($brand);
        }

        return $menu;
    }

    public function createClothingMenu()
    {
        $menu = $this->factory->createItem('clothing', [
            'uri' => '#',
            'linkAttributes' => [
                'data-target' => '#site-subnavbar-dropdown-clothing'
            ]
        ]);

        $menu->addChild('all', [
            'uri' => '#'
        ])->setLabel('View All Clothing');

        $brands = [
            'hoodies' => 'Hoodies',
            'jackets' => 'Jackets',
            'jeans' => 'Jeans',
            'knitwear' => 'Knitwear',
            'polos' => 'Polos',
            'shirts' => 'Shirts',
            'shorts' => 'Shorts',
            'sweaters' => 'Sweaters',
            'swimwear' => 'Swimwear',
            't-shirts' => 'T-Shirts',
            'tank-tops' => 'Tank Tops',
            'pants' => 'Pants',
            'underwear' => 'Underwear',
            'vests' => 'Vests',
        ];

        foreach ($brands as $slug => $brand) {
            $menu->addChild($slug, [
                'uri' => "#$slug"
            ])->setLabel($brand);
        }

        return $menu;
    }

    public function createAccessoriesMenu()
    {
        $menu = $this->factory->createItem('accessories', [
            'uri' => '#',
            'linkAttributes' => [
                'data-target' => '#site-subnavbar-dropdown-accessories'
            ]
        ]);

        $menu->addChild('all', [
            'uri' => '#'
        ])->setLabel('View All Accessories');

        $brands = [
            'bags' => 'Bags',
            'belts' => 'Belts',
            'cameras' => 'Cameras',
            'candles' => 'Candles',
            'cases' => 'Cases',
            'eyewear' => 'Eyewear',
            'grooming' => 'Grooming',
            'hats' => 'Hats',
            'headphones' => 'Headphones',
            'jewelry' => 'Jewelry',
            'keychains' => 'Keychains',
            'lifestyle' => 'Lifestyle',
            'music' => 'Music',
            'skateboards' => 'Skateboards',
            'socks' => 'Socks',
            'stationery' => 'Stationery',
            'umbrellas' => 'Umbrellas',
            'wallets' => 'Wallets',
            'watches' => 'Watches',
        ];

        foreach ($brands as $slug => $brand) {
            $menu->addChild($slug, [
                'uri' => "#$slug"
            ])->setLabel($brand);
        }

        return $menu;
    }

    public function createSaleMenu()
    {
        $menu = $this->factory->createItem('sale', [
            'route' => 'sylius_product_index_by_sale',
            'linkAttributes' => [
                'data-target' => '#site-subnavbar-dropdown-sale'
            ]
        ]);

        $brands = [
            '30-percent' => '30% off',
            '40-percent' => '40% off',
            '50-percent' => '50% off',
            '60-percent' => '60% off',
            '70-sale' => '70% off',
        ];

        foreach ($brands as $slug => $brand) {
            $menu->addChild($slug, [
                'uri' => "#$slug"
            ])->setLabel($brand);
        }

        return $menu;
    }
}
