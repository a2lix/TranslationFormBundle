<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    A2lix\TranslationFormBundle\Form\DataMapper\IndexByTranslationMapper;

/**
 *
 *
 * @author David ALLIX
 */
class TranslationsFormsType extends AbstractType
{
    private $locales;
    private $requiredLocales;

    /**
     *
     * @param type $locales
     * @param type $requiredLocales
     */
    public function __construct($locales, array $requiredLocales)
    {
        $this->locales = $locales;
        $this->requiredLocales = $requiredLocales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new IndexByTranslationMapper());

        $formOptions = isset($options['form_options']) ? $options['form_options'] : array();
        foreach ($options['locales'] as $locale) {
            $builder->add($locale, $options['form_type'],
                $formsOptions[$locale] + array('required' => in_array($locale, $options['required_locales']))
            );
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'by_reference' => false,
            //'required' => false,
            'required_locales' => $this->requiredLocales,
            'locales' => $this->locales,
            'form_type' => null,
            'form_options' => null,
        ));
    }

    public function getName()
    {
        return 'a2lix_translationsForms';
    }
}
