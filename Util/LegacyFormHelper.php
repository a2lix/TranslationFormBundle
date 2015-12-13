<?php

namespace A2lix\TranslationFormBundle\Util;

final class LegacyFormHelper
{
    private static $map = array(
        'A2lix\TranslationFormBundle\Form\Type\TranslationsType' => 'a2lix_translations',
        'A2lix\TranslationFormBundle\Form\Type\TranslationsFieldsType' => 'a2lix_translationsFields',
        'A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType' => 'a2lix_translationsForms',
        'A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType' => 'a2lix_translatedEntity',
        'A2lix\TranslationFormBundle\Form\Type\TranslationsLocalesSelectorType' => 'a2lix_translationsLocalesSelector',
    );

    public static function getType($class)
    {
        if (!self::isLegacy()) {
            return $class;
        }

        if (!isset(self::$map[$class])) {
            throw new \InvalidArgumentException(sprintf('Form type with class "%s" can not be found. Please check for typos or add it to the map in LegacyFormHelper', $class));
        }

        return self::$map[$class];
    }

    public static function isLegacy()
    {
        return !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
