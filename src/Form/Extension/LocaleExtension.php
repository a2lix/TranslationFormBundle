<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Extension;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\LocaleProvider\LocaleProviderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @phpstan-type LocaleFormOptionsDefaults array{
 *    default_locale: string,
 *    required_locales: list<string>,
 *    locales: list<string>,
 *    locale_labels: array<string, string>|null,
 *    theming_granularity: string,
 *    ...
 * }
 */
class LocaleExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly LocaleProviderInterface $localeProvider,
    ) {}

    #[\Override]
    public static function getExtendedTypes(): iterable
    {
        yield TranslationsFormsType::class;
        yield TranslationsType::class;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'default_locale' => $this->localeProvider->getDefaultLocale(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            'locales' => $this->localeProvider->getLocales(),
            'locale_labels' => null,
            'theming_granularity' => 'field',
        ]);

        $resolver->setAllowedTypes('locale_labels', 'array|null');
        $resolver->setAllowedValues('theming_granularity', ['field', 'locale_field']);
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['default_locale'] = $options['default_locale'];
        $view->vars['required_locales'] = $options['required_locales'];
    }
}
