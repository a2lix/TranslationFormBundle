<?php

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener;
use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use A2lix\TranslationFormBundle\TranslationForm\TranslationForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * @param TranslationForm           $translationForm
     * @param TranslationsFormsListener $translationsListener
     * @param LocaleProviderInterface   $localeProvider
     */
    public function __construct(
        TranslationForm $translationForm,
        TranslationsFormsListener $translationsListener,
        LocaleProviderInterface $localeProvider
    ) {
        $this->translationForm = $translationForm;
        $this->translationsListener = $translationsListener;
        $this->localeProvider = $localeProvider;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->translationsListener);

        $formsOptions = $this->translationForm->getFormsOptions($options);

        foreach ($options['locales'] as $locale) {
            if (isset($formsOptions[$locale])) {
                $builder->add($locale, $options['form_type'],
                    $formsOptions[$locale] + ['required' => in_array($locale, $options['required_locales'])]
                );
            }
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_locale'] = $this->localeProvider->getDefaultLocale();
        $view->vars['required_locales'] = $options['required_locales'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'empty_data' => function (FormInterface $form) {
                return new \Doctrine\Common\Collections\ArrayCollection();
            },
            'locales' => $this->localeProvider->getLocales(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            'form_type' => null,
            'form_options' => [],
        ]);
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
