<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\VariableProductBundle\Form\Type\VariableProductType as BaseProductType;
use Sylius\Bundle\CoreBundle\Model\Product;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Product form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductType extends BaseProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('shortDescription', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.product.short_description'
            ))
            ->add('supplierCode', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product.supplier_code'
            ))
            ->add('promotion', 'entity', array(
                'required' => false,
                'class'    => 'Sylius\Bundle\PromotionsBundle\Model\Promotion',
                'property' => 'name',
                'label'    => 'sylius.form.product.promotion'
            ))
            ->add('taxCategory', 'sylius_tax_category_choice', array(
                'required'    => false,
                'empty_value' => '---',
                'label'       => 'sylius.form.product.tax_category'
            ))
            ->add('shippingCategory', 'sylius_shipping_category_choice', array(
                'required'    => false,
                'empty_value' => '---',
                'label'       => 'sylius.form.product.shipping_category'
            ))
            ->add('taxons', 'sylius_taxon_selection')
            ->add('variantSelectionMethod', 'choice', array(
                'label'   => 'sylius.form.product.variant_selection_method',
                'choices' => Product::getVariantSelectionMethodLabels()
            ))
            ->add('restrictedZone', 'sylius_zone_choice', array(
                'empty_value' => 'sylius.form.product.restricted_zone.not_restricted',
                'label'       => 'sylius.form.product.restricted_zone',
                'required'    => false,
            ))
            ->add('status', 'choice', array(
                'label'   => 'sylius.form.product.status',
                'choices' => Product::getStatusLabels(),
                'expanded' => true,
            ))
            ->add('backInStockAt', 'datetime', [
                'label'       => 'sylius.form.product.back_in_stock_at',
                'date_format' => 'y-M-d',
                'date_widget' => 'choice',
                'time_widget' => 'text',
                'required'    => false,
            ])
            ->add('uploadId', 'hidden', array(
                'data'    => uniqid(),
                'mapped'  => false,
            ))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) {
                /** @var $data Product */
                $data = $event->getData();
                $form = $event->getForm();

                if ($data->getStatus() !== Product::STATUS_DRAFT) {
                    $form->add('publishedAt', 'datetime', [
                        'label'       => 'sylius.form.product.published_at',
                        'date_format' => 'y-M-d',
                        'date_widget' => 'choice',
                        'time_widget' => 'text',
                        'required'    => false,
                    ]);
                }
            }
        );
    }
}
