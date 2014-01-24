<?php

namespace Hypebeast\Bundle\WebBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class HomepageBannerSettingsSchema implements  SchemaInterface
{
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'banner_image_url_left'             => "",
                'banner_link_left'                  => "",
                'banner_large_title_left'           => 'Banner Title',
                'banner_subtitle_left'              => 'Shop Now',
                'banner_subtitle_font_size_left'    => 'medium',
                'banner_add_shop_now_btn_left'      => true,
                'banner_hover_effect_left'          => false,
                'banner_shop_now_color_left'        => 'white',

                'banner_image_url_right'             => "",
                'banner_link_right'                  => "",
                'banner_large_title_right'           => 'Banner Title',
                'banner_subtitle_right'              => 'Shop Now',
                'banner_subtitle_font_size_right'    => 'medium',
                'banner_add_shop_now_btn_right'      => true,
                'banner_hover_effect_right'          => false,
                'banner_shop_now_color_right'        => 'white'
            ))
        ;
    }

    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('banner_image_url_left', 'url', array(
                'default_protocol'  => 'http',
                'label'             => 'Banner Image URL',
                'constraints'       =>  array(
                    new NotBlank()
                )
            ))
            ->add('banner_link_left', 'url', array(
                'default_protocol'  => 'http',
                'label'             => 'Banner Link',
                'constraints'       => array(
                    new NotBlank()
                )
            ))
            ->add('banner_large_title_left', 'text', array(
                'label'         => 'Large Title',
                'constraints'   => array(
                    new NotBlank()
                )
            ))
            ->add('banner_subtitle_left', 'text', array(
                'label'         => 'Subtitle'
            ))
            ->add('banner_subtitle_font_size_left', 'choice', array(
                'label'     => 'Subtitle Font Size',
                'choices'   => array(
                    'small'     => 'Small',
                    'medium'    => 'Medium',
                    'large'     => 'Large'
                )
            ))
            ->add('banner_add_shop_now_btn_left', 'checkbox', array(
                'label'     => 'Add Shop Now Button',
                'value'     => true
            ))
            ->add('banner_hover_effect_left', 'checkbox', array(
                'label'     => 'Shop Now Hover Effect',
                'value'     => true
            ))
            ->add('banner_shop_now_color_left', 'choice', array(
                'label'     => 'Shop Now Color',
                'expanded'  => true,
                'multiple'  => false,
                'choices'   => array(
                    'white'     => 'White',
                    'black'     =>  'Black'
                )
            ))

            ->add('banner_image_url_right', 'url', array(
                'default_protocol'  => 'http',
                'label'             => 'Banner Image URL',
                'constraints'       =>  array(
                    new NotBlank()
                )
            ))
            ->add('banner_link_right', 'url', array(
                'default_protocol'  => 'http',
                'label'             => 'Banner Link',
                'constraints'       => array(
                    new NotBlank()
                )
            ))
            ->add('banner_large_title_right', 'text', array(
                'label'         => 'Large Title',
                'constraints'   => array(
                    new NotBlank()
                )
            ))
            ->add('banner_subtitle_right', 'text', array(
                'label'         => 'Subtitle'
            ))
            ->add('banner_subtitle_font_size_right', 'choice', array(
                'label'     => 'Subtitle Font Size',
                'choices'   => array(
                    'small'     => 'Small',
                    'medium'    => 'Medium',
                    'large'     => 'Large'
                )
            ))
            ->add('banner_add_shop_now_btn_right', 'checkbox', array(
                'label'     => 'Add Shop Now Button',
                'value'     => true
            ))
            ->add('banner_hover_effect_right', 'checkbox', array(
                'label'     => 'Shop Now Hover Effect',
                'value'     => true
            ))
            ->add('banner_shop_now_color_right', 'choice', array(
                'label'     => 'Shop Now Color',
                'expanded'  => true,
                'multiple'  => false,
                'choices'   => array(
                    'white'     => 'White',
                    'black'     =>  'Black'
                )
            ))
        ;
    }
}