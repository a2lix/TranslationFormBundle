<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 *
 * @author David ALLIX
 */
class TranslationsLocalesSelectorType extends AbstractType
{
    private $locales;

    /**
     *
     * @param type $locales
     */
    public function __construct($locales)
    {
        $this->locales = $locales;
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
            'choices' => array_combine($this->locales, $this->locales),
            'expanded' => true,
            'multiple' => true,
            'attr' => array(
                'class' => "a2lix_translationsLocalesSelector"
            )
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
        return 'a2lix_translationsLocalesSelector';
    }
}
