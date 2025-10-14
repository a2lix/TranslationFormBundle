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

use A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener;
use A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener;
use A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsLocalesSelectorType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;

return static function (ContainerConfigurator $container): void {
    $container->services()

    // Form Listeners
        ->set('a2lix_translation_form.form.event_listener.translations_listener', TranslationsListener::class)
        ->args([
            '$formManipulator' => service('a2lix_auto_form.manipulator.default')
        ])

        ->set('a2lix_translation_form.form.event_listener.translations_form_listener', TranslationsFormsListener::class)

    // Form Types
        ->set('a2lix_translation_form.form.type.translations_type', TranslationsType::class)
        ->args([
            '$translationsListener' => service('a2lix_translation_form.form.event_listener.translations_listener'),
            '$localeProvider' => service('a2lix_translation_form.locale_provider.default')
            ])
        ->tag('form.type')

        ->set('a2lix_translation_form.form.type.translations_forms_type', TranslationsFormsType::class)
        ->args([
            '$translationsFormsListener' => service('a2lix_translation_form.form.event_listener.translations_form_listener'),
            '$localeProvider' => service('a2lix_translation_form.locale_provider.default'),
            ])
        ->tag('form.type')

        ->set('a2lix_translation_form.form.type.translations_locales_selector_type', TranslationsLocalesSelectorType::class)
        ->args([
            '$localeProvider' => service('a2lix_translation_form.locale_provider.default')
        ])
        ->tag('form.type')

        ->set('a2lix_translation_form.form.type.translated_entity_type', TranslatedEntityType::class)
        ->args([
            '$requestStack' => service('request_stack')
        ])
        ->tag('form.type')
        ;
};