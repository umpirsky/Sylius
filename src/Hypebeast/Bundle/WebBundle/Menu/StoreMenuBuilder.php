<?php

namespace Hypebeast\Bundle\WebBundle\Menu;

use Knp\Menu\FactoryInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItem;
use Sylius\Bundle\CoreBundle\Model\Product;
use Sylius\Bundle\CoreBundle\Model\Taxon;
use Sylius\Bundle\WebBundle\Menu\MenuBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

class StoreMenuBuilder extends MenuBuilder
{
    protected $request;

    public function __construct(FactoryInterface $factory, SecurityContextInterface $securityContext, TranslatorInterface $translator, Request $request)
    {
        parent::__construct($factory, $securityContext, $translator);

        $this->request = $request;
    }

    public function createCartMenu(CartProviderInterface $cartProvider)
    {
        $cart = $cartProvider->getCart();
        $menu = $this->factory->createItem('root', [
            'route' => 'sylius_cart_summary',
            'attributes' => [
                'class' => 'dropdown'
            ],
            'linkAttributes' => [
                'class' => 'dropdown-toggle'
            ],
            'childrenAttributes' => [
                'class' => 'cart dropdown-menu pull-right'
            ]
        ]);

        /** @var $item OrderItem */
        foreach ($cart->getItems() as $item) {
            /** @var $product Product */
            $product = $item->getProduct();
            /** @var $brand Taxon */
            $brand = new Taxon();
            $brand->setName('N/A');
            $brand->setPermalink('na');

            if(count($brands = $product->getTaxons()->filter(function($taxon) { return 'Brand' === $taxon->getTaxonomy()->getName(); })) > 0) {
                $brand = $brands->first();
            }

            $menu->addChild($product->getSlug(),
                [
                    'route' => 'sylius_product_show',
                    'routeParameters' => ['slug' => $product->getSlug(), 'brand' => $brand->getPermalink()],
                    'labelAttributes' => [
                        'media_object' => true,
                    ],
                    'extras' => [
                        'safe_label' => true,
                        'media_object' => $product->getImage() ? $product->getImage()->getPath() : 'NA',
                        'media_heading' => $brand->getName(),
                        'media_body' => $product->getName(),
                        'pull' => 'pull-right'
                    ]
                ]
            );
        }

        if ($cart->countItems() > 0) {
            $menu->addChild('checkout', [
                'route' => 'sylius_cart_summary',
                'linkAttributes' => [
                    'class' => 'checkout-now'
                ]
            ])->setLabel($this->translate('Check Out Now'));
        }

        return $menu;
    }

    public function createUserMenu(CartProviderInterface $cartProvider)
    {
        $menu = $this->factory->createItem('user', [
            'childrenAttributes' => [
                'id' => 'site-account-menu',
            ],
        ]);

        $menu->addChild('location', [
            'uri' => '#',
            'labelAttributes' => [
                'icon' => 'glyphicon glyphicon-globe'
            ],
        ])->setLabel($this->request->getSession()->get('_hypebeast_default_country')->getIsoName());

        $menu->addChild($this->createCartMenu($cartProvider))
            ->setLabel($this->translate('%items% Items',
                ['%items%' => $cartProvider->getCart()->getTotalItems()]
            ));

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

    public function createStoreMenu()
    {
        $menu = $this->factory->createItem('store', [
            'route' => 'sylius_homepage'
        ]);

        $menu->addChild('home', [
            'route' => 'sylius_homepage'
        ])->setLabel('Home');
        $menu->addChild('new_arrivals', [
            'route' => 'store_new_arrivals'
        ])->setLabel('New Arrivals');
        $menu->addChild('back', [
            'route' => 'store_back_in_stock'
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

    public function createMobileMenu(CartProviderInterface $cartProvider)
    {
        $menu = $this->factory->createItem('root');

        $cart = $cartProvider->getCart();

        $menu->addChild('
            <form class="search-form" role="search" action="http://store.hypebeast.com">
                <input type="text" class="form-control" placeholder="Search Store" name="s" value="">
                <span class="glyphicon glyphicon-search"></span>
            </form>
        ',
            ['extras' => ['safe_label' => true]]
        );

        $menu->addChild('cart', array(
            'route' => 'sylius_cart_summary',
            'attributes' => [
                'class' => 'cart'
            ],
        ))->setLabel($this->translate('View Shipping Cart (%items% Items)', array(
            '%items%' => $cart->getTotalItems(),
        )));

        $menu->addChild(
            $this->factory->createItem('news', ['uri' => '#'])
                ->addChild('style', ['uri' => '#'])->setLabel('Style')->getParent()
                ->addChild('style', ['uri' => '#'])->setLabel('Arts')->getParent()
                ->addChild('design', ['uri' => '#'])->setLabel('Design')->getParent()
                ->addChild('music', ['uri' => '#'])->setLabel('Music')->getParent()
                ->addChild('entertainment', ['uri' => '#'])->setLabel('Entertainment')->getParent()
                ->addChild('lifestyle', ['uri' => '#'])->setLabel('Lifestyle')->getParent()
                ->addChild('tech', ['uri' => '#'])->setLabel('Tech')->getParent()
                ->addChild('editorial', ['uri' => '#'])->setLabel('Editorial')->getParent()
        )->setLabel('News');

        $menu->addChild(
            $this->createStoreMenu()->removeChild('home')
        )->setLabel('Store');

        $menu->addChild('forum', ['uri' => '#'])->setLabel('Forum');

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
                'route' => 'store_brand',
                'routeParameters' => [
                    'permalink' => $slug,
                ]
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

        foreach ($brands as $slug => $label) {
            $menu->addChild($slug, [
                'route' => 'store_subcategory',
                'routeParameters' => [
                    'parent' => 'clothing',
                    'permalink' => $slug,
                ]
            ])->setLabel($label);
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

        foreach ($brands as $slug => $label) {
            $menu->addChild($slug, [
                'route' => 'store_subcategory',
                'routeParameters' => [
                    'parent' => 'accessories',
                    'permalink' => $slug,
                ]
            ])->setLabel($label);
        }

        return $menu;
    }

    public function createSaleMenu()
    {
        $menu = $this->factory->createItem('sale', [
            'route' => 'store_sale',
            'linkAttributes' => [
                'data-target' => '#site-subnavbar-dropdown-sale'
            ]
        ]);

        $brands = [
            '30' => '30% off',
            '40' => '40% off',
            '50' => '50% off',
            '60' => '60% off',
            '70' => '70% off',
        ];

        foreach ($brands as $percentage => $label) {
            $menu->addChild($percentage, [
                'route' => 'store_sale_n_percent_off',
                'routeParameters' => [
                    'percentage' => $percentage
                ]
            ])->setLabel($label);
        }

        return $menu;
    }
}
