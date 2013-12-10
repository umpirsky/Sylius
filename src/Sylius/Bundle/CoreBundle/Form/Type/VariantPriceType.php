<?php

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VariantPriceType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', 'sylius_money', array(
                'label' => 'sylius.form.variant.price'
            ))
            ->add('salePrice', 'sylius_money', array(
                'label' => 'sylius.form.variant.sale_price'
            ))
            ->add('wholesalePrice', 'sylius_money', array(
                'label' => 'sylius.form.variant.wholesale_price'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass,
                'master'     => false
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_variant_price';
    }
}
