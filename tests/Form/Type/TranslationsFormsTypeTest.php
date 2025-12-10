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

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\Company;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\CompanyMediaLocale;
use A2lix\TranslationFormBundle\Tests\Fixtures\Form\CompanyMediaType;
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
#[CoversClass(TranslationsFormsType::class)]
#[AllowMockObjectsWithoutExpectations] // https://github.com/symfony/symfony/issues/62669
final class TranslationsFormsTypeTest extends TypeTestCase
{
    public function testEmptyManual(): void
    {
        $form = $this->factory->createBuilder(FormType::class, new Company())
            ->add('code')
            ->add('medias', TranslationsFormsType::class, [
                'form_type' => CompanyMediaType::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $this->assertCommonFormChildren($form);

        // Same with alternative creation
        $form = $this->factory->createBuilder(FormType::class, null, ['data_class' => Company::class])
            ->add('code')
            ->add('medias', TranslationsFormsType::class, [
                'form_type' => CompanyMediaType::class,
            ])
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
            ->add('medias', TranslationsFormsType::class, [
                'form_type' => CompanyMediaType::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $form->submit($submitData);
        $updCompany = $form->getData();

        self::assertSame('url en', $updCompany->getMedia('en')->url, 'Should be unchanged');
        self::assertSame('url frrrr', $updCompany->getMedia('fr')->url, 'Should be updated');
        self::assertSame('url deee', $updCompany->getMedia('de')->url, 'Should be created');
    }

    private function assertCommonFormChildren(FormInterface $form): void
    {
        self::assertFormChildren(
            [
                'en' => [
                    'expected_type' => CompanyMediaType::class,
                    'expected_children' => [
                        'url' => [
                            'expected_type' => CoreType\TextType::class,
                        ],
                    ],
                ],
                'fr' => [
                    'expected_type' => CompanyMediaType::class,
                    'expected_children' => [
                        'url' => [
                            'expected_type' => CoreType\TextType::class,
                        ],
                    ],
                ],
                'de' => [
                    'expected_type' => CompanyMediaType::class,
                    'expected_children' => [
                        'url' => [
                            'expected_type' => CoreType\TextType::class,
                        ],
                    ],
                ],
            ],
            $form->get('medias')->all(),
        );
    }

    private function commonCreateCompanyAndSubmit(): array
    {
        $company = new Company();
        $company->code = 'code1';

        $companyMediaLocaleEn = new CompanyMediaLocale();
        $companyMediaLocaleEn->id = 1;
        $companyMediaLocaleEn->locale = 'en';
        $companyMediaLocaleEn->url = 'url en';
        $companyMediaLocaleFr = new CompanyMediaLocale();
        $companyMediaLocaleFr->id = 2;
        $companyMediaLocaleFr->locale = 'fr';
        $companyMediaLocaleFr->url = 'url fr';

        $company->addMedia($companyMediaLocaleEn);
        $company->addMedia($companyMediaLocaleFr);

        $submitData = [
            'code' => 'code1',
            'medias' => [
                'en' => [
                    'url' => 'url en',
                ],
                'fr' => [
                    'url' => 'url frrrr',   // Upd
                ],
                'de' => [
                    'url' => 'url deee',    // New
                ],
            ],
        ];

        return [$company, $submitData];
    }
}
