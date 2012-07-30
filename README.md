# TranslationFormBundle
=====================

### What does this bundle ?

Adds a new type in your form named "translations" to facilitate the use of the Translatable Doctrine extension.


### Installation & Configuration

Add the repository in your composer.json

    "a2lix/translation-form-bundle" : "dev-master"

Enable the Bundle in the AppKernel.php

    new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),

Configure the Bundle in the config.yml

    a2lix_translation_form: ~

### Links and tutorial
You can find a common use case on [this repository](https://github.com/a2lix/DemoTranslationBundle).

There is also an article which is an example on how to manage translations with [SonataAdminBundle](https://github.com/sonata-project/SonataAdminBundle) available on [Elao's blog](http://www.elao.com/blog/symfony-2/doctrine-2/how-to-manage-translations-for-your-object-using-sonataadminbundle.html)

### Thanks to :
- [DoctrineExtensions][] & [StofDoctrineExtensionsBundle][]
- Contributors : [Tristan BESSOUSSA][]



[DoctrineExtensions]: https://github.com/l3pp4rd/DoctrineExtensions
[StofDoctrineExtensionsBundle]: https://github.com/stof/StofDoctrineExtensionsBundle
[Tristan BESSOUSSA]: https://github.com/tristanbes
