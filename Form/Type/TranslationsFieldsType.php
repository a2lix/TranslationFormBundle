<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Translations fields
 *
 * @author David ALLIX
 */
class TranslationsFieldsType extends AbstractType
{
    /**
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['fields'] as $fieldName => $fieldConfig) {
            $fieldType = $fieldConfig['field_type'];
            unset($fieldConfig['field_type']);

            $builder->add($fieldName, $fieldType, $fieldConfig);
        }
    }

    /**
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'fields' => array(),
        ));
    }

    public function getName()
    {
        return 'a2lix_translationsFields';
    }
}
