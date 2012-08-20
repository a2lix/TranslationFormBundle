<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Translations fields of a locale
 *
 * @author David ALLIX
 */
class TranslationsLocaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['fields'] as $fieldName => $fieldConfig) {
            $fieldType = $fieldConfig['type'];
            unset($fieldConfig['type']);
            
            $builder->add($fieldName, $fieldType, $fieldConfig);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true,
            'fields' => array(),
        ));
    }

    public function getName()
    {
        return 'translationsLocale';
    }
}
