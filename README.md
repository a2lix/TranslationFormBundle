# TranslationFormBundle
=====================

### What does this bundle ?

Offer the possibility to manage your Translatable fields from your entity easily with a new form type: 'a2lix_translation'
Screenshot and example with [this repository](https://github.com/a2lix/DemoTranslationBundle)


### Requirements

- Symfony2.1
- [StofDoctrineExtensionsBundle][] with translatable feature enabled
- Doctrine entities configured with [Personal Translation][] feature

### Installation & Configuration

Add the repository in your composer.json

    "a2lix/translation-form-bundle" : "dev-master"

Enable the Bundle in the AppKernel.php

    new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),

Configure the Bundle in the config.yml

    a2lix_translation_form:
        default_locale: en                  # [Optionnal] Default to 'en'
        locales: [fr, es, de]               # [Optionnal] Array of translations locales. Can be specified in the form. 
        default_required: false             # [Optionnal] Default to false. In this case, translation fields are not mark as required with html5

    # Template        
    twig:
        form:
            resources:
                - 'A2lixTranslationFormBundle::form.html.twig'

### Example

## Form

Minimal form example:

    $builder
        ->add('title')
        ->add('description')
        ->add('translations', 'translations')

Advanced form example:

    $builder
        ->add('title')
        ->add('description')
        ->add('translations', 'translations', array(
            'default_locale' => 'en'                    // [Optionnal] Override default_locale if already specified in the config.yml
            'locales' => array('fr', 'es', 'de')        // [Optionnal|Required] Override locales if already specified in the config.yml
            'fields' => array(                          // [Optionnal] Fields configurations. If not, auto detection from translatable annotations
                'title' => array(
                    'label' => 'name'                   // Custom label
                    'type' => 'textarea'                // Custom type : text or textarea. If not, auto detection from doctrine annotations
                ),
                'description' => array(
                    'display' => false
                )
            );
        ))
    ;

## Template

Separate the default locale from translations locales

    {{ form_widget(form.title) }}
    {{ form_widget(form.description) }}
    {{ form_widget(form.translations) }}

or group all locales in tabs with :

    {{ form_widget(form.translations, {'fields': [form.title, form.description]}) }}


### More help

You can find a common use case on [this repository](https://github.com/a2lix/DemoTranslationBundle). With translations for your collections as bonus.

There is also an article which is an example on how to manage translations with [SonataAdminBundle](https://github.com/sonata-project/SonataAdminBundle) available on [Elao's blog](http://www.elao.com/blog/symfony-2/doctrine-2/how-to-manage-translations-for-your-object-using-sonataadminbundle.html)



### Thanks to

- [DoctrineExtensions][] & [StofDoctrineExtensionsBundle][]
- Contributors : [Tristan BESSOUSSA][]



[DoctrineExtensions]: https://github.com/l3pp4rd/DoctrineExtensions
[Personal Translation]: https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/translatable.md#personal-translations
[StofDoctrineExtensionsBundle]: https://github.com/stof/StofDoctrineExtensionsBundle
[Tristan BESSOUSSA]: https://github.com/tristanbes
