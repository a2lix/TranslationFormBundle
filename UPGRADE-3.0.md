UPGRADE FROM 2.x to 3.x
=======================

## BC Breaks

### 1. New bundle requirements
```A2lix\AutoFormBundle\A2lixAutoFormBundle``` is required as an additional Bundle in your AppKernel.

If using Symfony 4
```php
// config/bundles.php

return [
    // ...
    A2lix\AutoFormBundle\A2lixAutoFormBundle::class => ['all' => true],
    // ...
];
```

If using Symfony 3
```php
// app/AppKernel.php

public function registerBundles()
{
    return [
        // ...
        new A2lix\AutoFormBundle\A2lixAutoFormBundle(),
        // ...
    ];
}
```

### 2. Config
```manager_registry``` option is no longer part of the ```a2lix_translation_form``` configuration file.

### 3. Creating forms with FormBuilder
```exclude_fields``` form option of `A2lix\TranslationFormBundle\Form\Type\TranslationsType`
was renamed to ```excluded_fields```.

You should fix all usages of this option otherwise exception will be thrown when building the form.

```php
$builder->add('translations', TranslationsType::class, [
    'excluded_fields' => ['details'], // use correct option name
]);
```

### 4. Overriding / extending services
Version 2 used parameters like `a2lix_translation_form.default.*.translations.class` to configure listeners, form types
and other services and you could override service classes by simply setting those parameters values in your app.

**Version 3 does not provide those parameters anymore**, so if you want to override any service provided by this bundle,
you have to do it in a compiler pass. E. g.:
```php
<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('a2lix_translation_form.form.event_listener.translations_listener')) {
            // change service definition to use your own implementation of TranslationsListener
            $container->getDefinition('a2lix_translation_form.form.event_listener.translations_listener')
                ->setClass(\App\EventListener\TranslationsListener::class);
        }
    }
}
```
You also have to register this compiler pass in your Kernel.

See [How to Work with Compiler Passes](https://symfony.com/doc/current/service_container/compiler_passes.html)
in Symfony docs for more details.

### 5. Bootstrap 4
Translations form template provided with v3 of this bundle requires Bootstrap v4. If you are using Bootstrap v3
and cannot upgrade at the moment, you should consider using custom template.
In particular this is the case when you're using TranslationFormBundle with SonataAdmin, which ships with
Bootstrap v3.

You can copy `default.html.twig` template from `2.x` branch into your app or create your own template
and configure translation form to use it:
```yaml
a2lix_translation_form:
  templating: "Translation/bootstrap_3_layout.html.twig"
```
