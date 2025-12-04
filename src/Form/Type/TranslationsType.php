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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsType extends AbstractType
{
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
        $resolver->setDefault('translation_class', static fn (Options $options): string => self::getTranslationClass($options['translatable_class']));
        $resolver->setDefault('gedmo', static fn (Options $options): bool => is_subclass_of($options['translation_class'], 'Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation'));
        $resolver->setDefault('inherit_data', static fn (Options $options): bool => $options['gedmo']);
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
            return $translatableClass::getTranslationEntityClass();
        }

        // Gedmo
        if (method_exists($translatableClass, 'getTranslationClass')) {
            return $translatableClass::getTranslationClass();
        }

        return $translatableClass.'Translation';
    }

    /**
     * @param non-empty-array<string, mixed> $options
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

        foreach ($options['locales'] as $locale) {
            $localeFormBuilder = $builder->create($locale, FormType::class, [
                'data_class' => $options['translation_class'],
                'setter' => static function ($translationColl, $translation, FormInterface $form) use ($locale): void {
                    if (null === $translation) {
                        return;
                    }

                    if ($translation->isEmpty()) {
                        $translationColl->removeElement($translation);

                        return;
                    }

                    $translation->setLocale($locale);
                    $translationColl->add($translation);
                },
                // LocaleExtension options process
                'label' => $formOptions['locale_labels'][$locale] ?? null,
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
     * @param non-empty-array<string, mixed> $options
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

        foreach ($options['locales'] as $locale) {
            $localeFormBuilder = $builder->create($locale, FormType::class, [
                ...(
                    $locale === $options['default_locale']
                    ? [
                        'inherit_data' => true,
                    ] : [
                        'getter' => static fn(mixed $translatable, FormInterface $form) => $translatable->getTranslations()->reduce(static function (array $acc, $item) use ($locale): array {
                            if ($item->getLocale() !== $locale) {
                                return $acc;
                            }

                            $acc[$item->getField()] = $item;

                            return $acc;
                        }, []),
                        'setter' => static function (mixed $translatable, $data, FormInterface $form): void {
                            foreach ($data as $translation) {
                                if (null === $translation->getContent()) {
                                    $translatable->removeTranslation($translation);
                                    continue;
                                }

                                $translatable->addTranslation($translation);
                            }
                        },
                    ]
                ),
                // LocaleExtension options process
                'label' => $formOptions['locale_labels'][$locale] ?? null,
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
                    'getter' => static fn (array $translations, FormInterface $form) => ($translations[$field] ?? null)?->getContent(),
                    'setter' => static function (array &$translations, $data, FormInterface $form) use ($field, $locale, $options): void {
                        // Update
                        if (null !== $translation = ($translations[$field] ?? null)) {
                            $translation->setContent($data);

                            return;
                        }

                        // Create
                        if (null !== $data) {
                            $translations[$field] = new ($options['translation_class'])($locale, $field, $data);
                        }
                    },
                ]);
            }

            $builder->add($localeFormBuilder);
        }
    }
}
