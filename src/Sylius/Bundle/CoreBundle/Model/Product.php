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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface;
use Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface;
use Sylius\Bundle\VariableProductBundle\Model\VariableProduct as BaseProduct;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use DateTime;

/**
 * Sylius core product entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Product extends BaseProduct implements ProductInterface
{
    /*
     * Variant selection methods.
     *
     * 1) Choice - A list of all variants is displayed to user.
     *
     * 2) Match  - Each product option is displayed as select field.
     *             User selects the values and we match them to variant.
     */
    const VARIANT_SELECTION_CHOICE = 'choice';
    const VARIANT_SELECTION_MATCH  = 'match';

    const STATUS_DRAFT = 0;
    const STATUS_UNPUBLISHED = 1;
    const STATUS_PUBLISHED = 2;

    /**
     * Short product description.
     * For lists displaying.
     *
     * @var string
     */
    protected $shortDescription;

    /**
     * @var string
     */
    protected $supplierCode;

    /**
     * self::STATUS_*
     *
     * @var int
     */
    protected $status;

    /**
     * Variant selection method.
     *
     * @var string
     */
    protected $variantSelectionMethod;

    /**
     * Taxons.
     *
     * @var Collection
     */
    protected $taxons;

    /**
     * Tax category.
     *
     * @var TaxCategoryInterface
     */
    protected $taxCategory;

    /**
     * Shipping category.
     *
     * @var ShippingCategoryInterface
     */
    protected $shippingCategory;

    /**
     * Not allowed to ship in this zone.
     *
     * @var ZoneInterface
     */
    protected $restrictedZone;

    /**
     * Gift card products have promotion defined.
     *
     * @var PromotionInterface
     */
    private $promotion;

    /**
     * Back in stock time.
     *
     * @var \DateTime
     */
    protected $backInStockAt;

    /**
     * Publish time.
     *
     * @var \DateTime
     */
    protected $publishedAt;

    protected $upSells;

    protected $crossSells;

    protected $crossSellOf;

    protected $guessedRelatedProducts;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setStatus(self::STATUS_DRAFT);
        $this->setMasterVariant(new Variant());
        $this->taxons = new ArrayCollection();
        $this->upSells = new ArrayCollection();
        $this->variantSelectionMethod = self::VARIANT_SELECTION_CHOICE;
        $this->giftCard = false;
    }

    public function isAvailable()
    {
        if ($this->isPublished() && !$this->isDeleted()) {
            return parent::isAvailable();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getMasterVariant()->getSku();
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        $this->getMasterVariant()->setSku($sku);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantSelectionMethod()
    {
        return $this->variantSelectionMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariantSelectionMethod($variantSelectionMethod)
    {
        if (!in_array($variantSelectionMethod, array(self::VARIANT_SELECTION_CHOICE, self::VARIANT_SELECTION_MATCH))) {
            throw new \InvalidArgumentException(sprintf('Wrong variant selection method "%s" given.', $variantSelectionMethod));
        }

        $this->variantSelectionMethod = $variantSelectionMethod;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isVariantSelectionMethodChoice()
    {
        return self::VARIANT_SELECTION_CHOICE === $this->variantSelectionMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariantSelectionMethodLabel()
    {
        $labels = self::getVariantSelectionMethodLabels();

        return $labels[$this->variantSelectionMethod];
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxons()
    {
        return $this->taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxons(Collection $taxons)
    {
        $this->taxons = $taxons;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->getMasterVariant()->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->getMasterVariant()->setPrice($price);

        return $this;
    }

    public function isOnSale()
    {
        if ($this->getSalePrice() > 0) {
            return true;
        }

        return false;
    }

    public function getPercentOff()
    {
        if ($this->isOnSale()) {
            return round((1-($this->getSalePrice() / $this->getPrice())) * 100, -1, PHP_ROUND_HALF_DOWN);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalePrice()
    {
        return $this->getMasterVariant()->getSalePrice();
    }

    /**
     * {@inheritdoc}
     */
    public function setSalePrice($salePrice)
    {
        $this->getMasterVariant()->setSalePrice($salePrice);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWholesalePrice()
    {
        return $this->getMasterVariant()->getWholesalePrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPrice()
    {
        return $this->getMasterVariant()->getCurrentPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function setWholesalePrice($wholesalePrice)
    {
        $this->getMasterVariant()->setWholesalePrice($wholesalePrice);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        if (self::STATUS_PUBLISHED == $status && self::STATUS_PUBLISHED != $this->status) {
            $this->setPublishedAt(new DateTime);
        }

        $this->status = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return self::STATUS_PUBLISHED == $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusLabel()
    {
        return $this->getStatusLabels()[$this->getStatus()];
    }

    /**
     * {@inheritdoc}
     */
    public function getSupplierCode()
    {
        return $this->supplierCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setSupplierCode($supplierCode)
    {
        $this->supplierCode = $supplierCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxCategory()
    {
        return $this->taxCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxCategory(TaxCategoryInterface $category = null)
    {
        $this->taxCategory = $category;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCategory()
    {
        return $this->shippingCategory;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingCategory(ShippingCategoryInterface $category = null)
    {
        $this->shippingCategory = $category;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRestrictedZone()
    {
        return $this->restrictedZone;
    }

    /**
     * {@inheritdoc}
     */
    public function setRestrictedZone(ZoneInterface $zone = null)
    {
        $this->restrictedZone = $zone;

        return $this;
    }

    public function getPromotion()
    {
        return $this->promotion;
    }

    public function setPromotion(PromotionInterface $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isGiftCard()
    {
        return null !== $this->getPromotion();
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->getMasterVariant()->getImages();
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getMasterVariant()->getImages()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getBackInStockAt()
    {
        return $this->backInStockAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setBackInStockAt($backInStockAt)
    {
        $this->backInStockAt = $backInStockAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get all possible related product.
     *
     * @param int $amount
     * @return ArrayCollection
     */
    public function getRelatedProducts($amount = 3)
    {
        $picked = $this->getPickedRelatedProducts();
        $guessed = $this->getGuessedRelatedProducts();

        if ($picked->count() >= $amount || empty($guessed)) {
            return $picked->slice(0, $amount);
        }

        $result = new ArrayCollection(array_merge(
            $picked->toArray(),
            $guessed->toArray()
        ));

        return $result->slice(0, $amount);
    }

    /**
     * Related products picked by store operator.
     *
     * @return ArrayCollection
     */
    public function getPickedRelatedProducts()
    {
        // Filter out sold out product
        $filter = function(Product $product) {
            return
                $product->isAvailable() &&
                $product->getMasterVariant()->getOnHand() > 0
            ;
        };

        return new ArrayCollection(
            array_merge(
                $this->getUpSells()->filter($filter)->toArray(),
                $this->getCrossSells()->filter($filter)->toArray()
            )
        );
    }

    /**
     * Related products guessed by system.
     *
     * @return ArrayCollection
     */
    public function getGuessedRelatedProducts()
    {
        return $this->guessedRelatedProducts;
    }

    public function setGuessedRelatedProducts($defaultUpSells)
    {
        if (is_array($defaultUpSells)) {
            $this->guessedRelatedProducts = new ArrayCollection($defaultUpSells);
        } else {
            $this->guessedRelatedProducts = $defaultUpSells;
        }
    }

    public function addGuessedRelatedProduct($defaultUpSell)
    {
        $this->guessedRelatedProducts->add($defaultUpSell);

        return $this;
    }

    public function hasGuessedRelatedProduct($defaultUpSell)
    {
        return $this->guessedRelatedProducts->contains($defaultUpSell);
    }

    public function removeGuessedRelatedProduct($defaultUpSell)
    {
        $this->guessedRelatedProducts->remove($defaultUpSell);

        return $this;
    }

    public function getUpSells()
    {
        return $this->upSells;
    }

    public function setUpSells(ArrayCollection $upSells)
    {
        $this->upSells = $upSells;

        return $this;
    }

    public function addUpSell($upSell)
    {
        $this->upSells->add($upSell);

        return $this;
    }

    public function hasUpSell($upSell)
    {
        return $this->upSells->contains($upSell);
    }

    public function removeUpSell($upSell)
    {
        $this->upSells->remove($upSell);

        return $this;
    }

    public function getCrossSells()
    {
        return $this->crossSells;
    }

    public function setCrossSells($crossSells)
    {
        $this->crossSells = $crossSells;

        return $this;
    }

    public function addCrossSell($crossSell)
    {
        $this->crossSells->add($crossSell);

        return $this;
    }

    public function hasCrossSell($crossSell)
    {
        return $this->crossSells->contains($crossSell);
    }

    public function removeCrossSell($crossSell)
    {
        $this->crossSells->remove($crossSell);

        return $this;
    }

    public function getCrossSellOf()
    {
        return $this->crossSellOf;
    }

    public function setCrossSellOf($crossSellOf)
    {
        $this->crossSellOf = $crossSellOf;

        return $this;
    }

    public function addCrossSellOf($crossSellOf)
    {
        $this->crossSellOf->add($crossSellOf);

        return $this;
    }

    public function hasCrossSellOf($crossSellOf)
    {
        return $this->crossSellOf->contains($crossSellOf);
    }

    public function removeCrossSellOf($crossSellOf)
    {
        $this->crossSellOf->remove($crossSellOf);

        return $this;
    }

    public static function getStatusLabels()
    {
        return array(
            self::STATUS_DRAFT       => 'Draft',
            self::STATUS_UNPUBLISHED => 'Unpublished',
            self::STATUS_PUBLISHED   => 'Published',
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getVariantSelectionMethodLabels()
    {
        return array(
            self::VARIANT_SELECTION_CHOICE => 'Variant choice',
            self::VARIANT_SELECTION_MATCH  => 'Options matching',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isNewArrival()
    {
        return $this->publishedAt > new \DateTime("-3 days");
    }
}
