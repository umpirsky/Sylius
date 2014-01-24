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

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface;
use Sylius\Bundle\TaxationBundle\Model\TaxableInterface;
use Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface;
use Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use DateTime;

/**
 * Product interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProductInterface extends VariableProductInterface, TaxableInterface
{
    /**
     * Get product SKU.
     *
     * @return string
     */
    public function getSku();

    /**
     * {@inheritdoc}
     */
    public function setSku($sku);

    public function getSupplierCode();
    public function setSupplierCode($supplierCode);

    /**
     * Get the variant selection method.
     *
     * @return string
     */
    public function getVariantSelectionMethod();

    /**
     * Set variant selection method.
     *
     * @param string $variantSelectionMethod
     */
    public function setVariantSelectionMethod($variantSelectionMethod);

    /**
     * Check if variant is selectable by simple variant choice.
     *
     * @return Boolean
     */
    public function isVariantSelectionMethodChoice();

    /**
     * Get pretty label for variant selection method.
     *
     * @return string
     */
    public function getVariantSelectionMethodLabel();

    /**
     * Get taxons.
     *
     * @return Collection
     */
    public function getTaxons();

    /**
     * Set categorization taxons.
     *
     * @param Collection $taxons
     */
    public function setTaxons(Collection $taxons);

    /**
     * Gets product price.
     *
     * @return integer $price
     */
    public function getPrice();

    /**
     * Sets product price.
     *
     * @param float $price
     */
    public function setPrice($price);

    public function isOnSale();
    public function getPercentOff();
    public function getSalePrice();
    public function setSalePrice($salePrice);
    public function getWholesalePrice();
    public function setWholesalePrice($wholesalePrice);
    public function getCurrentPrice();

    /**
     * Get product short description.
     *
     * @return string
     */
    public function getShortDescription();

    /**
     * Set product short description.
     *
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription);

    public function getStatus();
    public function setStatus($status);
    public function isPublished();
    public function getStatusLabel();

    /**
     * Set taxation category.
     *
     * @param TaxCategoryInterface $category
     */
    public function setTaxCategory(TaxCategoryInterface $category = null);

    /**
     * Get product shipping category.
     *
     * @return ShippingCategoryInterface
     */
    public function getShippingCategory();

    /**
     * Set product shipping category.
     *
     * @param ShippingCategoryInterface $category
     */
    public function setShippingCategory(ShippingCategoryInterface $category = null);

    /**
     * Get address zone restriction.
     *
     * @return ZoneInterface
     */
    public function getRestrictedZone();

    /**
     * Set address zone restriction.
     *
     * @param ZoneInterface $zone
     */
    public function setRestrictedZone(ZoneInterface $zone = null);

    public function getPromotion();
    public function setPromotion(PromotionInterface $promotion);
    public function isGiftCard();

    /**
     * Get all product images.
     *
     * @return Collection
     */
    public function getImages();

    /**
     * Get product main image.
     *
     * @return ImageInterface
     */
    public function getImage();

    public function getBackInStockAt();
    public function setBackInStockAt($backInStockAt);
    public function getPublishedAt();
    public function setPublishedAt($publishedAt);
    public function isNewArrival();
}
