<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
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

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'a2lix_translationsLocalesSelector';
    }

}
