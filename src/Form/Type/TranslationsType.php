<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Type;

use A2lix\AutoFormBundle\Form\Type\AutoType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @phpstan-type FormOptionsDefaults array{
 *    default_locale: string,
 *    enabled_locales: list<string>,
 *    required_locales: list<string>,
 *    locale_labels: array<string, string>|null,
 *    theming_granularity: string,
 *    translatable_class: class-string,
 *    translation_class: class-string,
 *    gedmo: bool,
 *    children: array<string, mixed>,
 *    children_excluded: list<string>|"*",
 *    children_embedded: list<string>|"*",
 *    children_groups: list<string>,
 *    builder: mixed|null,
 *    ...
 * }
 *
 * @extends AbstractType<mixed>
 */
class TranslationsType extends AbstractType
{
    /**
     * @param list<string> $globalExcludedChildren
     * @param list<string> $globalEmbeddedChildren
     */
    public function __construct(
        private readonly array $globalExcludedChildren = [],
        private readonly array $globalEmbeddedChildren = [],
    ) {}

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // FormType
            'by_reference' => false,
            // AutoType
            'children' => [],
            'children_excluded_' => $this->globalExcludedChildren,
            'children_excluded' => null,
            'children_embedded_' => $this->globalEmbeddedChildren,
            'children_embedded' => null,
            'children_groups' => ['Default'],
            'builder' => null,
        ]);

        $resolver->setRequired('translatable_class');
        $resolver->setAllowedTypes('translatable_class', 'string');
        $resolver->setDefault('translation_class', static fn (Options $options): string => self::getTranslationClass($options['translatable_class'])); // @phpstan-ignore argument.type
        $resolver->setDefault('gedmo', static fn (Options $options): bool => is_subclass_of(
            $options['translation_class'], // @phpstan-ignore argument.type
            'Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation'
        ));
        $resolver->setDefault('inherit_data', static fn (Options $options): bool => $options['gedmo']); // @phpstan-ignore return.type
        $resolver->setDefault('empty_data', static fn (Options $options): ?ArrayCollection => $options['gedmo'] ? null : new ArrayCollection());

        // AutoType
        $resolver->setAllowedTypes('children_excluded', 'string[]|string|callable|null');
        $resolver->setInfo('children_excluded', 'An array of properties, the * wildcard, or a callable (mixed $previousValue): mixed');
        $resolver->setNormalizer('children_excluded', static function (Options $options, mixed $value): mixed {
            if (\is_callable($value)) {
                return $value($options['children_excluded_']);
            }

            return $value ?? $options['children_excluded_'];
        });

        $resolver->setAllowedTypes('children_embedded', 'string[]|string|callable|null');
        $resolver->setInfo('children_embedded', 'An array of properties, the * wildcard, or a callable (mixed $previousValue): mixed');
        $resolver->setNormalizer('children_embedded', static function (Options $options, mixed $value): mixed {
            if (\is_callable($value)) {
                return $value($options['children_embedded_']);
            }

            return $value ?? $options['children_embedded_'];
        });

        $resolver->setAllowedTypes('children_groups', 'string[]|null');
        $resolver->setAllowedTypes('builder', 'callable|null');
        $resolver->setInfo('builder', 'A callable (FormBuilderInterface $builder, string[] $classProperties): void');
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FormOptionsDefaults $options */
        $options = $options;

        if ($options['gedmo']) {
            $this->buildGedmo($builder, $options);

            return;
        }

        $this->buildKnp($builder, $options);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'a2lix_translations';
    }

    private static function getTranslationClass(string $translatableClass): string
    {
        // Knp
        if (method_exists($translatableClass, 'getTranslationEntityClass')) {
            return $translatableClass::getTranslationEntityClass(); // @phpstan-ignore return.type
        }

        // Gedmo
        if (method_exists($translatableClass, 'getTranslationClass')) {
            return $translatableClass::getTranslationClass(); // @phpstan-ignore return.type
        }

        return $translatableClass.'Translation';
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param FormOptionsDefaults         $options
     */
    private function buildKnp(FormBuilderInterface $builder, array $options): void
    {
        // Build once optimization
        $builtChildren = $builder->create('tmp', AutoType::class, [
            'data_class' => $options['translation_class'],
            'children' => $options['children'],
            'children_excluded' => $options['children_excluded'],
            'children_embedded' => $options['children_embedded'],
            'children_groups' => $options['children_groups'],
            'builder' => $options['builder'],
        ])->all();

        foreach ($options['enabled_locales'] as $locale) {
            $localeFormBuilder = $builder->create($locale, FormType::class, [
                'data_class' => $options['translation_class'],
                'setter' => static fn (...$args) => self::knpLocaleSetter($locale, ...$args), // @phpstan-ignore argument.unpackNonIterable, argument.type
                // LocaleExtension options process
                'label' => $options['locale_labels'][$locale] ?? null,
                'required' => \in_array($locale, $options['required_locales'], true),
                'block_name' => ('field' === $options['theming_granularity']) ? 'locale' : null,
            ]);

            foreach ($builtChildren as $builtChild) {
                $localeFormBuilder->add($builtChild);
            }

            $builder->add($localeFormBuilder);
        }
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param FormOptionsDefaults         $options
     */
    private function buildGedmo(FormBuilderInterface $builder, array $options): void
    {
        // Build once optimization
        $builtChildren = $builder->create('tmp', AutoType::class, [
            'data_class' => $options['translatable_class'],
            'children' => $options['children'],
            'children_excluded' => $options['children_excluded'],
            'children_embedded' => $options['children_embedded'],
            'children_groups' => $options['children_groups'],
            'builder' => $options['builder'],
            'gedmo_only' => true,
        ])->all();

        $translationClass = $options['translation_class'];

        foreach ($options['enabled_locales'] as $locale) {
            $localeFormBuilder = $builder->create($locale, FormType::class, [
                ...(
                    $locale === $options['default_locale']
                    ? [
                        'inherit_data' => true,
                    ] : [
                        'getter' => static fn (...$args) => self::gedmoLocaleGetter($locale, ...$args), // @phpstan-ignore argument.unpackNonIterable, argument.type
                        'setter' => static fn (...$args) => self::gedmoLocaleSetter(...$args), // @phpstan-ignore argument.unpackNonIterable, argument.type
                    ]
                ),
                // LocaleExtension options process
                'label' => $options['locale_labels'][$locale] ?? null,
                'required' => \in_array($locale, $options['required_locales'], true),
                'block_name' => ('field' === $options['theming_granularity']) ? 'locale' : null,
            ]);

            foreach ($builtChildren as $builtChild) {
                // Translatable fields
                if ($locale === $options['default_locale']) {
                    $localeFormBuilder->add($builtChild);
                    continue;
                }

                // Translation fields/objects
                $field = $builtChild->getName();
                $localeFormBuilder->add($builtChild->getName(), $builtChild->getType()->getInnerType()::class, [
                    ...$builtChild->getFormConfig()->getOptions(),
                    'getter' => static fn (...$args) => self::gedmoFieldGetter($field, ...$args), // @phpstan-ignore argument.unpackNonIterable, argument.type
                    'setter' => static fn (array &$translations, ?string $data) => self::gedmoFieldSetter($field, $locale, $translationClass, $translations, $data),  // @phpstan-ignore-line
                    // 'property_path' => sprintf('[%s?].content', $field),
                ]);
            }

            $builder->add($localeFormBuilder);
        }
    }

    /**
     * @param Collection<int, \Stub\KnpTranslation> $translationColl
     * @param ?\Stub\KnpTranslation                 $translation
     */
    private static function knpLocaleSetter(string $locale, Collection $translationColl, ?object $translation): void
    {
        if (null === $translation) {
            return;
        }

        if ($translation->isEmpty()) {
            $translationColl->removeElement($translation);

            return;
        }

        $translation->setLocale($locale);
        $translationColl->add($translation);
    }

    /**
     * @param \Stub\GedmoTranslatable $translatable
     *
     * @return array<string, \Stub\GedmoTranslation>
     */
    private static function gedmoLocaleGetter(string $locale, object $translatable): array
    {
        /** @var array<string, \Stub\GedmoTranslation> */
        return $translatable->getTranslations()->reduce(
            static function (array $acc, object $item) use ($locale): array {
                if ($item->getLocale() !== $locale) {
                    return $acc;
                }

                $acc[$item->getField()] = $item;

                return $acc;
            },
            []
        );
    }

    /**
     * @param \Stub\GedmoTranslatable               $translatable
     * @param array<string, \Stub\GedmoTranslation> $data
     */
    private static function gedmoLocaleSetter(object $translatable, array $data): void
    {
        foreach ($data as $translation) {
            if (null === $translation->getContent()) {
                $translatable->removeTranslation($translation);
                continue;
            }

            $translatable->addTranslation($translation);
        }
    }

    /**
     * @param array<string, \Stub\GedmoTranslation> $translations
     */
    private static function gedmoFieldGetter(string $field, array $translations): ?string
    {
        return ($translations[$field] ?? null)?->getContent();
    }

    /**
     * @param class-string<\Stub\GedmoTranslation>  $translationClass
     * @param array<string, \Stub\GedmoTranslation> $translations
     */
    private static function gedmoFieldSetter(
        string $field,
        string $locale,
        string $translationClass,
        array &$translations,
        ?string $data,
    ): void {
        // Update
        if (null !== $translation = ($translations[$field] ?? null)) {
            $translation->setContent($data);

            return;
        }

        // Create
        if (null !== $data) {
            $translations[$field] = new ($translationClass)($locale, $field, $data);
        }
    }
}
