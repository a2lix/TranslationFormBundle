<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;
use A2lix\TranslationFormBundle\Form\DataMapper\GedmoTranslationMapper;

/**
* Translations locales
*
* @author David ALLIX
*/
class GedmoTranslationsLocalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isDefaultTranslation = ('defaultLocale' === $builder->getName());

        // Custom mapper for translations
        if (!$isDefaultTranslation) {
            $builder->setDataMapper(new GedmoTranslationMapper($options['translation_class']));
        }

        foreach ($options['locales'] as $locale) {
            if (isset($options['fields_options'][$locale])) {
                $builder->add($locale, 'a2lix_translationsFields', array(
                    'fields' => $options['fields_options'][$locale],
                    'inherit_data' => $isDefaultTranslation
                ));
            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => array(),
            'fields_options' => array(),
            'translation_class' => null
        ));
    }

    public function getName()
    {
        return 'a2lix_translationsLocales_gedmo';
    }
}