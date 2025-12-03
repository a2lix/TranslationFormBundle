<?php

declare(strict_types=1);

/*
 * This file is part of the AutoFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener;
use A2lix\TranslationFormBundle\Form\Extension\LocaleExtension;
use A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsLocalesSelectorType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\LocaleProvider\LocaleProviderInterface;
use A2lix\TranslationFormBundle\LocaleProvider\SimpleLocaleProvider;
use A2lix\TranslationFormBundle\Twig\Components\LocaleSwitcher;
use A2lix\TranslationFormBundle\Twig\LocaleExtension as TwigLocaleExtension;

return static function (ContainerConfigurator $container): void {
    $container->services()
        // Locale Provider
        ->set(SimpleLocaleProvider::class)
        ->args([
            '$locales' => abstract_arg('locales'),
            '$defaultLocale' => abstract_arg('defaultLocale'),
            '$requiredLocales' => abstract_arg('requiredLocales'),
        ])
        ->alias(LocaleProviderInterface::class, SimpleLocaleProvider::class)

        // Form Extensions
        ->set(LocaleExtension::class)
        ->args([
            '$localeProvider' => service(LocaleProviderInterface::class),
        ])
        ->tag('form.type_extension')

        // Form Types
        ->set(TranslationsType::class)
        ->tag('form.type')

        ->set(TranslationsFormsType::class)
        ->tag('form.type')

        ->set(TranslationsLocalesSelectorType::class)
        ->args([
            '$localeProvider' => service(LocaleProviderInterface::class),
        ])
        ->tag('form.type')

        ->set(TranslatedEntityType::class)
        ->args([
            '$localeSwitcher' => service('translation.locale_switcher'),
        ])
        ->tag('form.type')

        // Twig Components
        ->set(LocaleSwitcher::class)
        ->args([
            '$localeProvider' => service(LocaleProviderInterface::class),
        ])
        ->tag('twig.component', [
            'key' => 'A2lixTranslationForm:LocaleSwitcher',
            'template' => '@A2lixTranslationForm/components/LocaleSwitcher.html.twig',
        ])

        // Twig Extension
        ->set(TwigLocaleExtension::class)
        ->args([
            '$localeSwitcher' => service('translation.locale_switcher'),
        ])
        ->tag('twig.attribute_extension')
        ->tag('twig.runtime')
    ;
};
