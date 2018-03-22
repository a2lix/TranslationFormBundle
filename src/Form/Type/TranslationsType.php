<?php

declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener;
use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsType extends AbstractType
{
    /** @var TranslationsListener */
    private $translationsListener;
    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(TranslationsListener $translationsListener, LocaleProviderInterface $localeProvider)
    {
        $this->translationsListener = $translationsListener;
        $this->localeProvider = $localeProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber($this->translationsListener);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['default_locale'] = $options['default_locale'];
        $view->vars['required_locales'] = $options['required_locales'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'empty_data' => function (FormInterface $form) {
                return new ArrayCollection();
            },
            'locales' => $this->localeProvider->getLocales(),
            'default_locale' => $this->localeProvider->getDefaultLocale(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            'theming_granularity' => 'field',
            'fields' => [],
            'excluded_fields' => [],
        ]);

        $resolver->setAllowedValues('theming_granularity', ['field', 'locale_field']);
    }

    public function getBlockPrefix(): string
    {
        return 'a2lix_translations';
    }
}
