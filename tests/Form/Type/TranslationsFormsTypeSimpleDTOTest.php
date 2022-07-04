<?php

declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Tests\Fixtures\DTO\Form\ProductTranslationType;
use A2lix\TranslationFormBundle\Tests\Fixtures\DTO\Product;
use A2lix\TranslationFormBundle\Tests\Fixtures\DTO\ProductTranslation;
use A2lix\TranslationFormBundle\Tests\Form\TypeTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\PreloadedExtension;

/**
 * @internal
 */
final class TranslationsFormsTypeSimpleDTOTest extends TypeTestCase
{
    protected $locales = ['en', 'fr', 'de'];
    protected $defaultLocale = 'en';
    protected $requiredLocales = ['en', 'fr'];

    /**
     * @dataProvider provideEmptyDETranslationCases
     */
    public function testEmptyTranslation(array $formData, Product $productExpected): void
    {
        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => ProductTranslationType::class,
            ])
            ->getForm()
        ;

        $form->submit($formData);
        static::assertTrue($form->isSynchronized());
        static::assertEquals($productExpected, $form->getData());
    }

    public function provideEmptyDETranslationCases(): iterable
    {
        $product = new Product();
        $product->translations['en'] = (new ProductTranslation())
            ->setTitle('title en')
            ->setLocale('en')
        ;
        $product->translations['fr'] = (new ProductTranslation())
            ->setTitle('title fr')
            ->setLocale('fr')
        ;

        yield 'Translation DE is "null"' => [
            [
                'translations' => [
                    'en' => [
                        'title' => 'title en',
                    ],
                    'fr' => [
                        'title' => 'title fr',
                    ],
                    'de' => null,
                ],
            ],
            $product,
        ];

        yield 'Translation DE is an empty string' => [
            [
                'translations' => [
                    'en' => [
                        'title' => 'title en',
                    ],
                    'fr' => [
                        'title' => 'title fr',
                    ],
                    'de' => '',
                ],
            ],
            $product,
        ];

        yield 'Translation DE is "false"' => [
            [
                'translations' => [
                    'en' => [
                        'title' => 'title en',
                    ],
                    'fr' => [
                        'title' => 'title fr',
                    ],
                    'de' => false,
                ],
            ],
            $product,
        ];

        yield 'Translation DE does\'t exist' => [
            [
                'translations' => [
                    'en' => [
                        'title' => 'title en',
                    ],
                    'fr' => [
                        'title' => 'title fr',
                    ],
                ],
            ],
            $product,
        ];
    }

    /**
     * @dataProvider provideEmptyENTranslationCases
     */
    public function testEmptyAndRequiredTranslation(array $formData, Product $productExpected): void
    {
        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => ProductTranslationType::class,
            ])
            ->getForm()
        ;

        $form->submit($formData);
        static::assertTrue($form->isSynchronized());
        static::assertEquals($productExpected, $form->getData());
    }

    public function provideEmptyENTranslationCases(): iterable
    {
        $product = new Product();
        $product->translations['en'] = (new ProductTranslation())
            ->setLocale('en')
        ;
        $product->translations['fr'] = (new ProductTranslation())
            ->setTitle('title fr')
            ->setLocale('fr')
        ;
        $product->translations['de'] = (new ProductTranslation())
            ->setTitle('title de')
            ->setLocale('de')
        ;

        yield 'Translation EN is "null"' => [
            [
                'translations' => [
                    'en' => null,
                    'fr' => [
                        'title' => 'title fr',
                    ],
                    'de' => [
                        'title' => 'title de',
                    ],
                ],
            ],
            $product,
        ];

        yield 'Translation EN is an empty string' => [
            [
                'translations' => [
                    'en' => null,
                    'fr' => [
                        'title' => 'title fr',
                    ],
                    'de' => [
                        'title' => 'title de',
                    ],
                ],
            ],
            $product,
        ];

        yield 'Translation EN is "false"' => [
            [
                'translations' => [
                    'en' => false,
                    'fr' => [
                        'title' => 'title fr',
                    ],
                    'de' => [
                        'title' => 'title de',
                    ],
                ],
            ],
            $product,
        ];

        yield 'Translation EN does\'t exist' => [
            [
                'translations' => [
                    'fr' => [
                        'title' => 'title fr',
                    ],
                    'de' => [
                        'title' => 'title de',
                    ],
                ],
            ],
            $product,
        ];
    }

    protected function getExtensions(): array
    {
        $translationsFormsType = $this->getConfiguredTranslationsFormsType($this->locales, $this->defaultLocale, $this->requiredLocales);

        return [new PreloadedExtension([
            $translationsFormsType,
        ], [])];
    }
}
