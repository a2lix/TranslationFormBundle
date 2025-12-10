UPGRADE FROM 3.x to 4.x
=======================

## Breaking Changes

### PHP and Symfony Versions

- **Requires PHP 8.4+**
- **Requires Symfony 7.4|8.0**

### Configuration

The bundle configuration has been updated. Update your `config/packages/a2lix.yaml`:

**Before (v3):**
```yaml
a2lix_translation_form:
    locales: [en, fr, de]
    default_locale: en
```

**After (v4):**
```yaml
a2lix_translation_form:
    enabled_locales: [en, fr, de]   # Optional. Default from framework.enabled_locales
    default_locale: en   # Optional. Default from framework.default_locale
    required_locales: [en]   # Optional. Default []
```

Key changes:
- `locales` → `enabled_locales`

### Bootstrap 5 Template

The default template has changed from Bootstrap 4 to Bootstrap 5:

**Before:**
```yaml
templating: "@A2lixTranslationForm/bootstrap_4_layout.html.twig"
```

**After:**
```yaml
templating: "@A2lixTranslationForm/bootstrap_5_layout.html.twig"
```

Or use the framework-agnostic layout (No JS required, compatible with Gecko browsers):
```yaml
templating: "@A2lixTranslationForm/native_layout.html.twig"
```

The Bootstrap 5 template uses the new tab API with `data-bs-toggle` and `data-bs-target` attributes.

### Form Types Changes

#### TranslationsType

New options have been added with this type that rely on AutoType from [A2lixAutoFormBundle](https://github.com/a2lix/AutoFormBundle):

```php
$builder->add('translations', TranslationsType::class, [
    'translatable_class' => MyEntity::class,  // Required !
    'gedmo' => true,            // Required only if Gedmo !

    'default_locale' => ...       // Optional. From LocaleExtension
    'enabled_locales' => ...      // Optional. From LocaleExtension
    'required_locales' => ...     // Optional. From LocaleExtension
    'locale_labels' => ...        // Optional. From LocaleExtension
    'theming_granularity' => ...  // Optional. From LocaleExtension
    'children_excluded' => ...    // Optional. See AutoFormBundle
    'children_embedded' => ...    // Optional. See AutoFormBundle
    'children_groups' => ...      // Optional. See AutoFormBundle
    'children' => ...             // Optional. See AutoFormBundle
    'builder' => ...              // Optional. See AutoFormBundle
]);
```

Key changes:
- `translatable_class` is now required


### LocaleProvider System

The LocaleProviderInterface::getLocales() method has been renamed to LocaleProviderInterface::getEnabledLocales()

### Twig Components

#### LocaleSwitcher Component (New)

New Twig component for locale switching:

```twig
<twig:A2lixTranslationForm:LocaleSwitcher />
```

Available renders:
- `basic`: Inline badges
- `dropdown`: Bootstrap dropdown menu
