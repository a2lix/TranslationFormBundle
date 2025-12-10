<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Form\Type;

use A2lix\AutoFormBundle\Form\Type\AutoType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\Product;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\ProductTranslation;
use A2lix\TranslationFormBundle\Tests\Form\TypeTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Extension\Core\Type as CoreType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;

/**
 * @internal
 */
#[CoversClass(TranslationsType::class)]
#[AllowMockObjectsWithoutExpectations] // https://github.com/symfony/symfony/issues/62669
final class GedmoTranslationsTypeTest extends TypeTestCase
{
    public function testEmptyManual(): void
    {
        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('code')
            ->add('translations', TranslationsType::class, [
                'translatable_class' => Product::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);

        // Same with alternative creation
        $form = $this->factory->createBuilder(FormType::class, null, ['data_class' => Product::class])
            ->add('code')
            ->add('translations', TranslationsType::class, [
                'translatable_class' => Product::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);
    }

    public function testEmptyAuto(): void
    {
        $form = $this->factory->createBuilder(AutoType::class, new Product())
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);

        // Same with alternative creation
        $form = $this->factory->createBuilder(AutoType::class, null, ['data_class' => Product::class])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);
    }

    public function testModifyManual(): void
    {
        [$product, $submitData] = $this->commonCreateProductAndSubmit();

        $form = $this->factory->createBuilder(FormType::class, $product)
            ->add('code')
            ->add('translations', TranslationsType::class, [
                'translatable_class' => Product::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $form->submit($submitData);
        $updProduct = $form->getData();

        self::assertSame('title en', $updProduct->title, 'Should be unchanged');
        self::assertSame('desc ennnnnnn', $updProduct->description, 'Should be updated');
        self::assertSame('title frrrrrr', self::getGedmoTranslation($updProduct, 'fr', 'title')->getContent(), 'Should be updated');
        self::assertSame(self::getGedmoTranslation($product, 'fr', 'description'), self::getGedmoTranslation($updProduct, 'fr', 'description'), 'Should be unchanged');
        self::assertSame('title deeee', self::getGedmoTranslation($updProduct, 'de', 'title')?->getContent(), 'Should be created');
        self::assertNull(self::getGedmoTranslation($updProduct, 'de', 'description')?->getContent(), 'Should be unchanged');
    }

    public function testModifyAuto(): void
    {
        [$product, $submitData] = $this->commonCreateProductAndSubmit();

        $form = $this->factory->createBuilder(AutoType::class, $product)
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $form->submit($submitData);
        $updProduct = $form->getData();

        self::assertSame('title en', $updProduct->title, 'Should be unchanged');
        self::assertSame('desc ennnnnnn', $updProduct->description, 'Should be updated');
        self::assertSame('title frrrrrr', self::getGedmoTranslation($updProduct, 'fr', 'title')->getContent(), 'Should be updated');
        self::assertSame(self::getGedmoTranslation($product, 'fr', 'description'), self::getGedmoTranslation($updProduct, 'fr', 'description'), 'Should be unchanged');
        self::assertSame('title deeee', self::getGedmoTranslation($updProduct, 'de', 'title')?->getContent(), 'Should be created');
        self::assertNull(self::getGedmoTranslation($updProduct, 'de', 'description')?->getContent(), 'Should be unchanged');
    }

    private function assertCommonFormChildren(FormInterface $form): void
    {
        self::assertFormChildren(
            [
                'en' => [
                    'expected_type' => FormType::class,
                    'expected_children' => [
                        'title' => [
                            'expected_type' => CoreType\TextType::class,
                        ],
                        'description' => [
                            'expected_type' => CoreType\TextareaType::class,
                        ],
                    ],
                ],
                'fr' => [
                    'expected_type' => FormType::class,
                    'expected_children' => [
                        'title' => [
                            'expected_type' => CoreType\TextType::class,
                            'required' => true,
                        ],
                        'description' => [
                            'expected_type' => CoreType\TextareaType::class,
                        ],
                    ],
                ],
                'de' => [
                    'expected_type' => FormType::class,
                    'expected_children' => [
                        'title' => [
                            'expected_type' => CoreType\TextType::class,
                        ],
                        'description' => [
                            'expected_type' => CoreType\TextareaType::class,
                        ],
                    ],
                ],
            ],
            $form->get('translations')->all(),
        );
    }

    private function commonCreateProductAndSubmit(): array
    {
        $product = new Product();
        $product->code = 'code1';
        $product
            ->addTranslation(new ProductTranslation('en', 'title', 'title en'))
            ->addTranslation(new ProductTranslation('en', 'description', 'desc en'))
            ->addTranslation(new ProductTranslation('fr', 'title', 'title fr'))
            ->addTranslation(new ProductTranslation('fr', 'description', 'desc fr'))
        ;

        $submitData = [
            'code' => 'code1',
            'translations' => [
                'en' => [
                    'title' => 'title en',
                    'description' => 'desc ennnnnnn',  // Upd translatable description
                ],
                'fr' => [
                    'title' => 'title frrrrrr',   // Upd translation FR title
                    'description' => 'desc fr',
                ],
                'de' => [
                    'title' => 'title deeee',     // New translation DE title
                    'description' => '',
                ],
            ],
        ];

        return [$product, $submitData];
    }

    private static function getGedmoTranslation(object $translatable, string $locale, string $field): ?object
    {
        return $translatable->getTranslations()->findFirst(
            static fn (int $k, $t) => $locale === $t->getLocale() && $field === $t->getField()
        );
    }
}
