<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Type;

use A2lix\AutoFormBundle\Form\Type\AutoType;
use A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener;
use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsType extends AbstractType
{
    public function __construct(
        private readonly LocaleProviderInterface $localeProvider,
        private readonly TranslationsListener $translationsListener,
    ) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber($this->translationsListener);
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['default_locale'] = $options['default_locale'];
        $view->vars['required_locales'] = $options['required_locales'];
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'locales' => $this->localeProvider->getLocales(),
            'default_locale' => $this->localeProvider->getDefaultLocale(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            //
            'locale_labels' => null,
            'theming_granularity' => 'field',
        ]);

        $resolver->setAllowedValues('theming_granularity', ['field', 'locale_field']);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'a2lix_translations';
    }
}
