# A2lix Translation Form Bundle

Translate your doctrine objects easily with some helps

[![Latest Stable Version](https://poser.pugx.org/a2lix/translation-form-bundle/v/stable)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Latest Unstable Version](https://poser.pugx.org/a2lix/translation-form-bundle/v/unstable)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![License](https://poser.pugx.org/a2lix/translation-form-bundle/license)](https://packagist.org/packages/a2lix/translation-form-bundle)

[![Total Downloads](https://poser.pugx.org/a2lix/translation-form-bundle/downloads)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Monthly Downloads](https://poser.pugx.org/a2lix/translation-form-bundle/d/monthly)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Daily Downloads](https://poser.pugx.org/a2lix/translation-form-bundle/d/daily)](https://packagist.org/packages/a2lix/translation-form-bundle)

| Branch | Tools |
| --- | --- |
| 3.x (master) | [![Build Status][v3_ci_badge]][v3_ci_link] [![Coverage Status][v3_coverage_badge]][v3_coverage_link] |

## Screenshot example

![A2LiX Translation Form Screenshot](/a2lix_translationForm.jpg)

## Support

* `3.x` depends on [AutoFormBundle](https://github.com/a2lix/AutoFormBundle) and has higher requirements (PHP8.1+, Symfony5.4+/6.3+/7.0+). It is compatible with [KnpLabs](https://github.com/KnpLabs/DoctrineBehaviors#translatable), [A2lix](https://github.com/a2lix/I18nDoctrineBundle) and [Prezent](https://github.com/Prezent/doctrine-translatable-bundle)

## Installation

Use composer:

```bash
composer require a2lix/translation-form-bundle
```

After the successful installation, add/check the bundle registration:

```php
// Symfony >= 4.0 in bundles.php
// ...
A2lix\AutoFormBundle\A2lixAutoFormBundle::class => ['all' => true],
A2lix\TranslationFormBundle\A2lixTranslationFormBundle::class => ['all' => true],
// ...

// Symfony >= 3.4 in AppKernel::registerBundles()
$bundles = array(
// ...
new A2lix\AutoFormBundle\A2lixAutoFormBundle(),
new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
// ...
```

## Configuration

There is no minimal configuration. Full list of optional parameters:

```yaml
# Symfony >= 4.0. Create a dedicated a2lix.yaml in config/packages with:
# Symfony >= 3.4. Add in your app/config/config.yml:

a2lix_translation_form:
    locale_provider: default       # [1]
    locales: [en, fr, es, de]      # [1-a]
    default_locale: en             # [1-b]
    required_locales: [fr]         # [1-c]
    templating: "@A2lixTranslationForm/bootstrap_4_layout.html.twig"      # [2]
```

1. Custom locale provider service id. Default one relies on [1-*] values:
   - [1-a] List of translations locales to display
   - [1-b] Default locale
   - [1-c] List of required translations locales
2. The default template is Twitter Bootstrap compatible. You can redefine your own here

## Usage

### In a classic formType

```php
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
...
$builder->add('translations', TranslationsType::class);
```

### Advanced examples

```php
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
...
$builder->add('translations', TranslationsType::class, [
    'locales' => ['en', 'fr', 'es', 'de'],   // [1]
    'default_locale' => ['en'],              // [1]
    'required_locales' => ['fr'],            // [1]
    'fields' => [                               // [2]
        'description' => [                         // [3.a]
            'field_type' => 'textarea',                // [4]
            'label' => 'descript.',                    // [4]
            'locale_options' => [                  // [3.b]
                'es' => ['label' => 'descripción'],    // [4]
                'fr' => ['display' => false]           // [4]
            ]
        ]
    ],
    'excluded_fields' => ['details'],           // [2]
    'locale_labels' => [                        // [5]
        'fr' => 'Français',
        'en' => 'English',
    ],
]);
```

1. Optional. If set, override the default value from config.yml
2. Optional. If set, override the default value from config.yml
3. Optional. If set, override the auto configuration of fields
   - [3.a] Optional. - For a field, applied to all locales
   - [3.b] Optional. - For a specific locale of a field
4. Optional. Common options of symfony forms (max_length, required, trim, read_only, constraints, ...), which was added 'field_type' and 'display'
5. Optional. Set the labels for the translation tabs. Default to the name of the locale. Translation keys can be used here.

## Additional

### TranslationsFormsType

A different approach for entities which don't share fields untranslated. No strategy used here, only a locale field in your entity.

```php
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
...
$builder->add('translations', TranslationsFormsType::class, [
    'locales' => ['en', 'fr', 'es', 'de'],   // [1]
    'default_locale' => ['en']               // [1]
    'required_locales' => ['fr'],            // [1]
    'form_type' => ProductMediaType::class,     // [2 - Mandatory]
    'form_options' => [                         // [2bis]
         'context' => 'pdf'
    ]
]);
```

1. Optional. If set, override the default value from config.yml
2. Mandatory. A real form type that you have to do
   - [2bis] Optional. - An array of options that you can set to your form

### TranslatedEntityType

Modified version of the native 'entity' symfony form type to translate the label in the current locale by reading translations

```php
use A2lix\TranslationFormBundle\Form\Type\TranslatedEntityType;
...
$builder->add('medias', TranslatedEntityType::class, [
    'class' => 'A2lix\DemoTranslationBundle\Entity\Media',   // [1 - Mandatory]
    'translation_property' => 'title',                           // [2 - Mandatory]
    'multiple' => true,                                             // [3]
]);
```

1. Path of the translatable class
2. Property/Method of the translatable class that will be display
3. Common options of the 'entity' Symfony form type (multiple, ...)

### Example

See [Demo Bundle](https://github.com/a2lix/Demo) for more examples.


## Contribution help

```
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer install --ignore-platform-reqs
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer run-script phpunit
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer run-script cs-fixer
```

## License

This package is available under the [MIT license](LICENSE).

[v3_ci_badge]: https://github.com/a2lix/TranslationFormBundle/actions/workflows/ci.yml/badge.svg
[v3_ci_link]: https://github.com/a2lix/TranslationFormBundle/actions/workflows/ci.yml
[v3_coverage_badge]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/master/graph/badge.svg
[v3_coverage_link]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/master
