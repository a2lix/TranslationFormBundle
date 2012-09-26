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

### Entity ([example](https://github.com/a2lix/DemoTranslationBundle/blob/master/src/A2lix/DemoTranslationBundle/Entity/Product.php))

```php
<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity\Product.php
 *
 * @ORM\Table()
 * @Gedmo\TranslationEntity(class="Translation\ProductTranslation")
 */
class Product
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     * @Gedmo\Translatable
     */
    private $description;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Translation\ProductTranslation",
     * 	mappedBy="object",
     * 	cascade={"persist", "remove"}
     * )
     * @Assert\Valid(deep = true)
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set translations
     *
     * @param ArrayCollection $translations
     * @return Product
     */
    public function setTranslations($translations)
    {
        $this->translations = $translations;
        return $this;
    }

    /**
     * Get translations
     *
     * @return ArrayCollection 
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add translation
     *
     * @param ProductTranslation
     */
    public function addTranslation($translation)
    {
        if ($translation->getContent()) {
            $translation->setObject($this);
            $this->translations->add($translation);
        }
    }

    /**
     * Remove translation
     *
     * @param ProductTranslation
     */
    public function removeTranslation($translation)
    {
        $this->translations->removeElement($translation);
    }

}
```

### Personal Translation Entity ([example](https://github.com/a2lix/DemoTranslationBundle/blob/master/src/A2lix/DemoTranslationBundle/Entity/Translation/ProductTranslation.php))

```php
<?php

namespace Entity\Translation;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Entity\Translation\ProductTranslation.php
 
 * @ORM\Entity
 * @ORM\Table(name="product_translations",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id", "field"
 *   })}
 * )
 */
class ProductTranslation extends AbstractPersonalTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="Entity\Product", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;
}
```

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
