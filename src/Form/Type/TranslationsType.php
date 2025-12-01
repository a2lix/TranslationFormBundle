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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsType extends AbstractType
{
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'translatable_class' => null,
            'gedmo' => false,
            // AutoType options
            'children' => [],
            'children_excluded' => [],
            'children_embedded' => [],
            'children_groups' => [],
            'builder' => null,
        ]);

        $resolver->setDefault('translation_class', static fn (Options $options): string => self::getTranslationClass($options['translatable_class']));
        $resolver->setDefault('inherit_data', static fn (Options $options): bool => $options['gedmo']);
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

    private function buildGedmo(FormBuilderInterface $builder, array $options): void
    {
        // Build once optimization
        $builtChildren = $builder->create('tmp', AutoType::class, [
            'data_class' => $options['translatable_class'],
            'gedmo_only' => true,
        ])->all();

        foreach ($options['locales'] as $locale) {
            $localeFormBuilder = $builder->create($locale, FormType::class, [
                ...(
                    $locale === $options['default_locale']
                    ? [
                        'inherit_data' => true,
                    ] : [
                        'getter' => static function ($translatable, FormInterface $form) use ($locale) {
                            return $translatable->getTranslations()->reduce(static function (array $acc, $item) use ($locale) {
                                if ($item->getLocale() !== $locale) {
                                    return $acc;
                                }

                                $acc[$item->getField()] = $item;

                                return $acc;
                            }, []);
                        },
                        'setter' => static function ($translatable, $data, FormInterface $form): void {
                            $translationColl = $translatable->getTranslations();
                            foreach ($data as $translation) {
                                if (null === $translation->getContent()) {
                                    $translationColl->removeElement($translation);
                                    continue;
                                }

                                $translationColl->add($translation->setObject($translatable));
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
                if ($locale === $options['default_locale']) {
                    $localeFormBuilder->add($builtChild);
                    continue;
                }

                $field = $builtChild->getName();
                $localeFormBuilder->add($builtChild->getName(), $builtChild->getType()->getInnerType()::class, [
                    ...$builtChild->getFormConfig()->getOptions(),
                    'getter' => static fn ($translations, FormInterface $form) => $translations[$field]?->getContent(),
                    'setter' => static function ($translations, $data, FormInterface $form) use ($field, $locale, $options): void {
                        if (null !== $translation = $translations[$field]) {
                            $translation->setContent($data);

                            return;
                        }

                        if (null !== $data) {
                            $translations[$field] = new ($options['translation_class'])($locale, $field, $data);
                        }
                    },
                ]);
            }

            $builder->add($localeFormBuilder);
        }
    }

    private function buildKnp(FormBuilderInterface $builder, array $options): void
    {
        // Build once optimization
        $builtChildren = $builder->create('tmp', AutoType::class, [
            'data_class' => $options['translation_class'],
        ])->all();

        foreach ($options['locales'] as $locale) {
            $localeFormBuilder = $builder->create($locale, FormType::class, [
                'data_class' => $options['translation_class'],
                // LocaleExtension options process
                'label' => $formOptions['locale_labels'][$locale] ?? null,
                'required' => \in_array($locale, $options['required_locales'], true),
                'block_name' => ('field' === $options['theming_granularity']) ? 'locale' : null,

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
            ]);

            foreach ($builtChildren as $builtChild) {
                $localeFormBuilder->add($builtChild);
            }

            $builder->add($localeFormBuilder);
        }
    }
}
