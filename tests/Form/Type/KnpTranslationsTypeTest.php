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
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\Company;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\CompanyTranslation;
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
final class KnpTranslationsTypeTest extends TypeTestCase
{
    public function testEmptyManual(): void
    {
        $emptyForm = $this->factory->createBuilder(FormType::class, new Company())
            ->add('code')
            ->add('translations', TranslationsType::class, [
                'translatable_class' => Company::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($emptyForm);

        // Same with alternative creation
        $form = $this->factory->createBuilder(FormType::class, null, ['data_class' => Company::class])
            ->add('code')
            ->add('translations', TranslationsType::class, [
                'translatable_class' => Company::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);
    }

    public function testEmptyAuto(): void
    {
        $form = $this->factory->createBuilder(AutoType::class, new Company())
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);

        // Same with alternative creation
        $form = $this->factory->createBuilder(AutoType::class, null, ['data_class' => Company::class])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);
    }

    public function testModifyManual(): void
    {
        [$company, $submitData] = $this->commonCreateCompanyAndSubmit();

        $form = $this->factory->createBuilder(FormType::class, $company)
            ->add('code')
            ->add('translations', TranslationsType::class, [
                'translatable_class' => Company::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $form->submit($submitData);
        $updCompany = $form->getData();

        self::assertSame('title en', $updCompany->getTranslations()['en']->title, 'Should be unchanged');
        self::assertSame('desc ennnnnnn', $updCompany->getTranslations()['en']->description, 'Should be updated');
        self::assertSame('title frrrrrr', $updCompany->getTranslations()['fr']->title, 'Should be updated');
        self::assertSame($company->getTranslations()['fr']->description, $updCompany->getTranslations()['fr']->description, 'Should be unchanged');
        self::assertSame('title deeee', $updCompany->getTranslations()['de']?->title, 'Should be created');
        self::assertNull($updCompany->getTranslations()['de']?->description, 'Should be unchanged');
    }

    public function testModifyAuto(): void
    {
        [$company, $submitData] = $this->commonCreateCompanyAndSubmit();

        $form = $this->factory->createBuilder(AutoType::class, $company)
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $form->submit($submitData);
        $updCompany = $form->getData();

        self::assertSame('title en', $updCompany->getTranslations()['en']->title, 'Should be unchanged');
        self::assertSame('desc ennnnnnn', $updCompany->getTranslations()['en']->description, 'Should be updated');
        self::assertSame('title frrrrrr', $updCompany->getTranslations()['fr']->title, 'Should be updated');
        self::assertSame($company->getTranslations()['fr']->description, $updCompany->getTranslations()['fr']->description, 'Should be unchanged');
        self::assertSame('title deeee', $updCompany->getTranslations()['de']?->title, 'Should be created');
        self::assertNull($updCompany->getTranslations()['de']?->description, 'Should be unchanged');
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

    private function commonCreateCompanyAndSubmit(): array
    {
        $company = new Company();
        $company->code = 'code1';

        $companyTranslationEn = new CompanyTranslation();
        $companyTranslationEn->setLocale('en');
        $companyTranslationEn->title = 'title en';
        $companyTranslationEn->description = 'description en';
        $companyTranslationFr = new CompanyTranslation();
        $companyTranslationFr->setLocale('fr');
        $companyTranslationFr->title = 'title fr';
        $companyTranslationFr->description = 'description fr';

        $company->addTranslation($companyTranslationEn);
        $company->addTranslation($companyTranslationFr);

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

        return [$company, $submitData];
    }
}
