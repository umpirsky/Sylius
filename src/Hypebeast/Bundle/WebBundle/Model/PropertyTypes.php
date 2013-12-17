<?php

namespace Hypebeast\Bundle\WebBundle\Model;

final class PropertyTypes
{
    const TEXT     = 'text';
    const TEXTAREA = 'textarea';
    const NUMBER   = 'number';
    const CHOICE   = 'choice';
    const CHECKBOX = 'checkbox';

    public static function getChoices()
    {
        return array(
            self::TEXT     => 'Text',
            self::TEXTAREA => 'Textarea',
            self::NUMBER   => 'Number',
            self::CHOICE   => 'Choice',
            self::CHECKBOX => 'Checkbox',
        );
    }
}
