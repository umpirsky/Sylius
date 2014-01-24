<?php

namespace Hypebeast\Bundle\CoreBundle\Form\Type\Checkout;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryAndPaymentStepType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        $notBlank->message = 'sylius.checkout.payment_method.not_blank';

        $builder
            ->add('shipments', 'collection', array(
                'type'    => 'sylius_checkout_shipment',
                'options' => array('criteria' => $options['criteria'])
            ))
            ->add('paymentMethod', 'sylius_payment_method_choice', array(
                'label'         => 'sylius.form.checkout.payment_method',
                'expanded'      => true,
                'property_path' => 'payment.method',
                'constraints'   => array(
                    $notBlank
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
            ->setOptional(array(
                'criteria'
            ))
            ->setAllowedTypes(array(
                'criteria' => array('array')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'hypebeast_checkout_delivery_and_payment';
    }
}
