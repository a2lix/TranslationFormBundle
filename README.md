# A2lix TranslationForm Bundle

[![Latest Stable Version](https://poser.pugx.org/a2lix/translation-form-bundle/v/stable)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Latest Unstable Version](https://poser.pugx.org/a2lix/translation-form-bundle/v/unstable)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Total Downloads](https://poser.pugx.org/a2lix/translation-form-bundle/downloads)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![License](https://poser.pugx.org/a2lix/translation-form-bundle/license)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Build Status](https://github.com/a2lix/TranslationFormBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/a2lix/TranslationFormBundle/actions/workflows/ci.yml)

A small, flexible Symfony bundle that helps you build forms to manage translations for Doctrine entities. It integrates with common translation strategies (Gedmo Personal Translation and Knp DoctrineBehaviors) and provides form types, helpers and Twig components to make working with multilingual data easier.

Key features
- Easy form handling for translatable entities (Knp & Gedmo strategies).
- Support for one-record-per-locale patterns via `TranslationsFormsType`.
- `TranslatedEntityType` for entity choice labels using translations.
- Centralized locale configuration via `LocaleProvider`.
- Twig helpers and a `LocaleSwitcher` component.

> [!NOTE]
> Use [A2lixAutoFormBundle](https://github.com/a2lix/AutoFormBundle) for automatic form generation and customization.

> [!TIP]
> A complete demonstration is also available at [a2lix/demo](https://github.com/a2lix/Demo).

## Installation

- Install the bundle with Composer:
```bash
composer require a2lix/translation-form-bundle
```

## Basic configuration

Add a minimal configuration in `config/packages/a2lix.yaml`:

```yaml
a2lix_translation_form:
    enabled_locales: [en, fr, de]   # Optional. Default from framework.enabled_locales
    default_locale: en   # Optional. Default from framework.default_locale
    required_locales: [en]   # Optional. Default []
    # templating: "@A2lixTranslationForm/bootstrap_5_layout.html.twig"
```

If you keep the default setup, the bundle will automatically prepend the chosen form theme (Bootstrap 5 layout by default) into Twig's `form_themes`.

Compatibility: Gedmo & Knp

- [Gedmo PersonalTranslation](https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/translatable.md#personal-translations): Fully supported. When using Gedmo's personal translation mapping, the bundle renders translation fields as separate translation objects and manages creation and removal of `Gedmo` translation entities.
- [KnpDoctrineBehaviors](https://github.com/KnpLabs/DoctrineBehaviors/blob/master/docs/translatable.md): Fully supported. For Knp-style translations (one translation object per locale), fields are bound directly to locale forms.

## Usage examples

#### TranslationsType (Knp or Gedmo)
```php
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

$builder->add('translations', TranslationsType::class, [
    'translatable_class' => App\Entity\Post::class,
    // Optional:
    // 'locale_labels' => ['en' => 'English', 'fr' => 'Français'],
    // 'theming_granularity' => 'field', // or 'locale_field'
]);
```

#### TranslationsFormsType (one-record-per-locale)
```php
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;

$builder->add('medias', TranslationsFormsType::class, [
    'form_type' => App\Form\CompanyMediaType::class,
    'form_options' => [
        'data_class' => App\Entity\CompanyMediaLocale::class,
    ],
]);
```

#### TranslatedEntityType (entity choices with translation labels)
```php
use A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType;

$builder->add('category', TranslatedEntityType::class, [
    'class' => App\Entity\Category::class,
    'translation_property' => 'title',
]);
```

#### Locale selection widget
```php
use A2lix\TranslationFormBundle\Form\Type\TranslationsLocalesSelectorType;

$builder->add('locales', TranslationsLocalesSelectorType::class, [
    // uses the bundle's LocaleProvider to populate choices
]);
```

## Twig helpers & components

Locale rendering function:
```twig
{{ locale_render('en') }}                     {# -> 'English' (localized) #}
{{ locale_render('en', 'locale_upper') }}     {# -> 'EN' #}
{{ locale_render('fr', 'locale_name_title') }}{# -> 'Français' #}
```

LocaleSwitcher component:
```twig
{# Render basic badges #}
<twig:A2lixTranslationForm:LocaleSwitcher render="basic" />

{# Render dropdown #}
<twig:A2lixTranslationForm:LocaleSwitcher render="dropdown" />
```

## LocaleProvider

The bundle centralizes locale configuration through a `LocaleProviderInterface`. By default, `SimpleLocaleProvider` is registered and configured from bundle settings. You can replace it with your own service by changing `locale_provider` in the bundle configuration.

## Integration with AutoFormBundle

This bundle integrates cleanly with `a2lix/auto-form-bundle`. When using `AutoType` with translatable entities, `TranslationsType` and `TranslationsFormsType` can be automatically configured and rendered based on entity metadata and bundle options.

## License
This package is available under the MIT license — see the LICENSE file.