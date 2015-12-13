<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use A2lix\TranslationFormBundle\TranslationForm\TranslationForm,
    A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener,
    A2lix\TranslationFormBundle\Locale\LocaleProviderInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author David ALLIX
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslationsFormsType extends AbstractType
{
    private $translationForm;
    private $translationsListener;
    private $localeProvider;

    /**
     *
     * @param \A2lix\TranslationFormBundle\TranslationForm\TranslationForm              $translationForm
     * @param \A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener $translationsListener
     * @param \A2lix\TranslationFormBundle\Locale\LocaleProviderInterface               $localeProvider
     */
    public function __construct(TranslationForm $translationForm, TranslationsFormsListener $translationsListener,  LocaleProviderInterface $localeProvider)
    {
        $this->translationForm = $translationForm;
        $this->translationsListener = $translationsListener;
        $this->localeProvider = $localeProvider;
    }

    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->translationsListener);

        $formsOptions = $this->translationForm->getFormsOptions($options);

        foreach ($options['locales'] as $locale) {
            if (isset($formsOptions[$locale])) {
                $builder->add($locale, $options['form_type'],
                    $formsOptions[$locale] + array('required' => in_array($locale, $options['required_locales']))
                );
            }
        }
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
        $view->vars['required_locales'] = $this->localeProvider->getRequiredLocales();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'by_reference' => false,
            'empty_data' => function (FormInterface $form) {
                return new \Doctrine\Common\Collections\ArrayCollection();
            },
            'locales' => $this->localeProvider->getLocales(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            'form_type' => null,
            'form_options' => array(),
        ));
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }


    // BC for SF < 3.0
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'a2lix_translationsForms';
    }
}
