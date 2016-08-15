<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
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

    /**
     * BC for SF < 2.7
     * 
     * {@inheritdoc}
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
            'by_reference' => false,
            //'required' => false,
            'required_locales' => $this->requiredLocales,
            'locales' => $this->locales,
            'form_type' => null,
            'form_options' => null,
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
        return 'a2lix_translationsForms';
    }
}
