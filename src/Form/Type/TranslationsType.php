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
use A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsType extends AbstractType
{
    public function __construct(
        private readonly TranslationsListener $translationsListener,
    ) {}

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'translatable_class' => null,
            'translation_class' => null,
            'gedmo' => false,
            // AutoType options
            'children' => [],
            'children_excluded' => [],
            'children_embedded' => [],
            'children_groups' => [],
            'builder' => null,
        ]);

        $resolver->setDefault('inherit_data', static function (Options $options): bool {
            return $options['gedmo'];
        });
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $validAutoTypeOptions = $options;
        unset(
            // TranslationsType options
            $validAutoTypeOptions['translation_class'],
            // LocaleExtension options
            $validAutoTypeOptions['default_locale'],
            $validAutoTypeOptions['required_locales'],
            $validAutoTypeOptions['locales'],
            $validAutoTypeOptions['locale_labels'],
            $validAutoTypeOptions['theming_granularity'],
        );

        // Build once optimization
        $builtChildren = $builder->create('tmp', AutoType::class, [
            ...(
                $options['gedmo'] ? [
                    'data_class' => $options['translatable_class'],
                    'gedmo_only' => true,
                ] : [
                    'data_class' => $options['translation_class'],
                ]
            ),
        ])->all();

        dump($builtChildren);

        foreach ($options['locales'] as $locale) {
            if ($options['gedmo']) {
                $localeFormBuilder = $builder->create($locale, FormType::class, [
                    ...(
                        $locale === $options['default_locale']
                            ? [
                                'inherit_data' => true,
                                // 'data_class' => $options['translatable_class'],
                                // 'property_path' =>
                            ] : [
                                // 'data_class' => $options['translation_class'],
                                // 'getter' => function ($translatable, FormInterface $form) use ($locale) {
                                //     // return $translatable->getTranslations()->get($locale);

                                //     // $aa = array_column($translatable->getTranslations()->get($locale), 'field', 'content');

                                //     $l = $translatable->getTranslations()->get($locale);
                                //     $aa = null !== $l ? [
                                //         $l->getField() => $l->getContent(),
                                //     ] : [];

                                //     // $aa =  array_reduce(
                                //     //     $translatable->getTranslations()->get($locale),
                                //     //     function (array $carry, $item) {
                                //     //         $carry[$item->getLocale()][$item->getField()] = $item->getContent();
                                //     //         return $carry;
                                //     //     },
                                //     //     []
                                //     // );

                                //     dump($aa);

                                //     return $aa;
                                // },
                                // 'setter' => function ($translatable, $data, FormInterface $form) use ($options, $locale): void {
                                //     dump($translatable, $data, $form);

                                    // loop

                                    //     foreach ($data as $field => $value) {
                                //         if (null === $value) {
                                //             continue;
                                //         }

                                //         dump($translatable, $data, $form);

                                //         $translatable->addTranslation(
                                //             new ($options['translation_class'])($locale, $field, $value)
                                //         );
                                //     }
                                // },
                                'getter' => function ($translatable, FormInterface $form) use ($locale) {
                                    return $translatable->getTranslations()->filter(static fn ($elt): bool => $locale === $elt->getLocale());
                                },
                                'setter' => function ($translatable, $data, FormInterface $form) use ($options, $locale): void {
                                            dump($translatable, $data, $form);
                                    foreach ($data as $d) {
                                        $translatable->getTranslations()->add($d->setObject($translatable));
                                    }
                                },
                                // 'property_path' => 'translations'// sprintf('translations[%s]', $locale),
                            ]
                    ),

                    // LocaleExtension options process
                    'label' => $formOptions['locale_labels'][$locale] ?? null,
                    'required' => \in_array($locale, $options['required_locales'], true),
                    'block_name' => ('field' === $options['theming_granularity']) ? 'locale' : null,
                ]);//->addEventSubscriber($this->translationsListener);


                foreach ($builtChildren as $builtChild) {
                    if ($locale === $options['default_locale']) {
                        $localeFormBuilder->add($builtChild);
                    } else {
                        $field = $builtChild->getName();
                        $localeFormBuilder->add($builtChild->getName(), $builtChild->getType()->getInnerType()::class, [
                            ...$builtChild->getFormConfig()->getOptions(),
                            // 'data_class' => $options['translation_class'],
                            'getter' => function ($translations, FormInterface $form) use ($field) {
                                dump($translations);
                                return $translations->findFirst(static fn (int $key, $elt): bool => $field === $elt->getField())?->getContent();
                            },
                            'setter' => function ($translations, $data, FormInterface $form) use ($field, $locale, $options): void {
                                $translation = $translations->findFirst(static fn (int $key, $elt): bool => $field === $elt->getField());

                                // Empty case?
                                if (null === $data) {
                                    if (null !== $translation) {
                                        $translations->removeElement($translation);
                                        return;
                                    }

                                    return;
                                }

                                if (null !== $translation) {
                                    $translation->setContent($data);
                                    return;
                                }

                                $translations->add(
                                    new ($options['translation_class'])($locale, $field, $data)
                                );
                            }
                                // 'setter' => function ($translatable, $data, FormInterface $form) use ($options, $locale): void {
                                //     foreach ($data as $field => $value) {
                                //         if (null === $value) {
                                //             continue;
                                //         }

                                //         dump($translatable, $data, $form);

                                //         $translatable->addTranslation(
                                //             new ($options['translation_class'])($locale, $field, $value)
                                //         );
                                //     }
                                // },
                            // 'property_path' => 'content'
                        ]);
                    }
                }


            // KNP
            } else {
                $localeFormBuilder = $builder->create($locale, FormType::class, [
                    'data_class' => $options['translation_class'],
                    // LocaleExtension options process
                    'label' => $formOptions['locale_labels'][$locale] ?? null,
                    'required' => \in_array($locale, $options['required_locales'], true),
                    'block_name' => ('field' === $options['theming_granularity']) ? 'locale' : null,
                ]);//->addEventSubscriber($this->translationsListener);


                foreach ($builtChildren as $builtChild) {
                    $localeFormBuilder->add($builtChild);
                }
            }



            $builder->add($localeFormBuilder);
        }

        $builder->addEventSubscriber($this->translationsListener);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'a2lix_translations';
    }
}
