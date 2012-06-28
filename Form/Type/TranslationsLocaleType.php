<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationsLocaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($options['fields'] as $field => $type) {
            $builder->add($field, $type, array(
                'label' => ucfirst($field),
                'required' => false
            ));
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
