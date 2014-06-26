<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    A2lix\TranslationFormBundle\Form\DataMapper\GedmoTranslationMapper;

/**
 * Translations locales (gedmo)
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
            $builder->setDataMapper(new GedmoTranslationMapper());
        }

        foreach ($options['locales'] as $locale) {
            if (isset($options['fields_options'][$locale])) {
                $builder->add($locale, 'a2lix_translationsFields', array(
                    'fields' => $options['fields_options'][$locale],
                    'translation_class' => $options['translation_class'],
                    'inherit_data' => $isDefaultTranslation,
                    'required' => in_array($locale, $options['required_locales'])
                ));
            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => array(),
            'fields_options' => array(),
            'translation_class' => null,
            'required_locales' => null
        ));
    }

    public function getName()
    {
        return 'a2lix_translationsLocales_gedmo';
    }
}
