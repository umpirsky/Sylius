<?php

namespace Hypebeast\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\CartItemType as BaseCartItemType;
use Sylius\Bundle\CoreBundle\Model\Product;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Fix https://github.com/101medialab/HypebeastStore/issues/57
 */
class CartItemType extends BaseCartItemType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if (isset($options['product']) && $options['product']->hasVariants()) {
            $choiceList = function (Options $options) {
                /** @var $product Product */
                $product = $options['product'];

                return new ObjectChoiceList($product->getAvailableVariants(), 'options[0].value', [], null, 'id');
            };

            $builder->add('variant', 'sylius_variant_choice', [
                'choice_list' => $choiceList,
                'expanded' => false,
                'label' => 'sylius.product.color',
                'product'  => $options['product']
            ]);
        }
    }
}
