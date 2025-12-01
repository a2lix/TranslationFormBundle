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

return static function (ContainerConfigurator $container): void {
    $container->services()
        // Locale Provider
        ->set('a2lix_translation_form.locale_provider.simple_locale_provider', SimpleLocaleProvider::class)
        ->args([
            '$locales' => abstract_arg('locales'),
            '$defaultLocale' => abstract_arg('defaultLocale'),
            '$requiredLocales' => abstract_arg('requiredLocales'),
        ])
        ->alias(LocaleProviderInterface::class, 'a2lix_translation_form.locale_provider.simple_locale_provider')

        // Form Extensions
        ->set('a2lix_translation_form.form.extension.locale_extension', LocaleExtension::class)
        ->args([
            '$localeProvider' => service(LocaleProviderInterface::class),
        ])
        ->tag('form.type_extension')

        // Form Types
        ->set('a2lix_translation_form.form.type.translations_type', TranslationsType::class)
        ->tag('form.type')

        ->set('a2lix_translation_form.form.type.translations_forms_type', TranslationsFormsType::class)
        ->tag('form.type')

        ->set('a2lix_translation_form.form.type.translations_locales_selector_type', TranslationsLocalesSelectorType::class)
        ->args([
            '$localeProvider' => service(LocaleProviderInterface::class),
        ])
        ->tag('form.type')

        ->set('a2lix_translation_form.form.type.translated_entity_type', TranslatedEntityType::class)
        ->args([
            '$localeSwitcher' => service('translation.locale_switcher'),
        ])
        ->tag('form.type')

        // Twig Components
        ->set(LocaleSwitcher::class)
        ->args([
            '$localeProvider' => service(LocaleProviderInterface::class),
            '$localeSwitcher' => service('translation.locale_switcher'),
        ])
        ->tag('twig.component', [
            'key' => 'A2lixTranslationForm:LocaleSwitcher',
            'template' => '@A2lixTranslationForm/components/LocaleSwitcher_bootstrap_5.html.twig',
        ])
    ;
};
