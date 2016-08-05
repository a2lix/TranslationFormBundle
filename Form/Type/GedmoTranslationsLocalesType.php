<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
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
                ));
            }
        }
    }

    /**
     * BC for SF < 2.7
     * 
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => array(),
            'fields_options' => array(),
            'translation_class' => null
        ));
    }

    /**
     * BC for SF < 2.8
     *
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'a2lix_translationsLocales_gedmo';
    }
}
