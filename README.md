# TranslationFormBundle

## What does this bundle?

Offers the possibility to easily manage the translatable fields of your entity with a new form type: 'a2lix_translations'.

[This repository](https://github.com/a2lix/DemoTranslationBundle) contains a screenshot and example code.


## Requirements

- Symfony2.1
- [StofDoctrineExtensionsBundle][] with the translatable feature enabled
- Doctrine entities configured with the [personal translations][] feature

## Installation & Configuration

Add the repository to your composer.json

    "a2lix/translation-form-bundle": "dev-master"

Run Composer to install the bundle

    php composer.phar update a2lix/translation-form-bundle

Enable the bundle in AppKernel.php

    new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),

Configure the bundle in config.yml

```yaml
a2lix_translation_form:
    default_locale: en          # [optional] Defaults to 'en'. Must be the same as the 'default_locale' of the stof_doctrine_extensions
    locales: [fr, es, de]       # [optional] Array of the translation locales (The default locale have to be excluded). Can also be specified in the form builder.
    default_required: false     # [optional] Defaults to false. In this case, translation fields are not mark as required with HTML5.

# Template
twig:
    form:
        resources:
            - 'A2lixTranslationFormBundle::form.html.twig'
```

## Example

### Form

Minimal form example:

```php
$builder
    ->add('title')
    ->add('description')
    ->add('translations', 'a2lix_translations')
;
```

Advanced form example:

```php
$builder
    ->add('title')
    ->add('description')
    ->add('translations', 'a2lix_translations', array(
        'default_locale' => 'en',               // [optional] See above
        'locales' => array('fr', 'es', 'de'),   // [optional|required - depends on the presence in config.yml] See above
        'required' => true,                     // [optional] Overrides default_required if need
        'fields' => array(                      // [optional] Manual configuration of fields to display and options. If not specified, all translatable fields will be display, and options will be auto-detected
            'title' => array(
                'label' => 'name',              // [optional] Custom label. Ucfirst, otherwise
                'type' => 'textarea',           // [optional] Custom type
                **OTHER_OPTIONS**               // [optional] max_length, required, trim, read_only, constraints, ...
            ),
            'description' => array(
                'label' => 'Desc.'              // [optional]
                'locale_options' => array(              // [optional] Manual configuration of field for a dedicated locale -- Higher priority
                    'fr' => array(
                        'label' => 'descripción'        // [optional] Higher priority
                        **OTHER_OPTIONS**               // [optional] Same possibilities as above
                    ),
                    'es' => array(
                        'display' => false              // [optional] Prevent display of the field for this locale
                    )
                )
            ),
        );
    ))
;
```

### Template

Separate the default locale from translation locales

```html+jinja
{{ form_widget(form.title) }}
{{ form_widget(form.description) }}
{{ form_widget(form.translations) }}
```

or group all locales in tabs with

```html+jinja
{{ form_widget(form.translations, {'fields': [form.title, form.description]}) }}
```

## More help

You can find a common use case in [this repository](https://github.com/a2lix/DemoTranslationBundle). With translations for your collections as bonus.

There is also an article with an example on how to manage translations with [SonataAdminBundle](https://github.com/sonata-project/SonataAdminBundle) available on [Elao's blog](http://www.elao.com/blog/symfony-2/doctrine-2/how-to-manage-translations-for-your-object-using-sonataadminbundle.html).

## Thanks to

- [DoctrineExtensions][] & [StofDoctrineExtensionsBundle][]
- Contributors: [Tristan BESSOUSSA][]



[DoctrineExtensions]: https://github.com/l3pp4rd/DoctrineExtensions
[personal translations]: https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/translatable.md#personal-translations
[StofDoctrineExtensionsBundle]: https://github.com/stof/StofDoctrineExtensionsBundle
[Tristan BESSOUSSA]: https://github.com/tristanbes
