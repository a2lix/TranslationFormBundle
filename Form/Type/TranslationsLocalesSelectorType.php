<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author David ALLIX
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslationsLocalesSelectorType extends AbstractType
{
    private $localeProvider;

    /**
     *
     * @param \A2lix\TranslationFormBundle\Locale\LocaleProviderInterface $localeProvider
     */
    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_locale'] = $this->localeProvider->getDefaultLocale();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array_combine($this->localeProvider->getLocales(), $this->localeProvider->getLocales()),
            'expanded' => true,
            'multiple' => true,
            'attr' => array(
                'class' => "a2lix_translationsLocalesSelector"
            )
        ));
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getParent()
    {
        return 'choice';
    }

    // BC for SF < 3.0
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'a2lix_translationsLocalesSelector';
    }
}
