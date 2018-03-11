<?php

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Gedmo\Form;

use A2lix\TranslationFormBundle\Tests\Gedmo\Fixtures\Entity\Product;
use A2lix\TranslationFormBundle\Tests\Gedmo\Fixtures\Entity\ProductTranslation;
use A2lix\TranslationFormBundle\Tests\TranslationsTypeTestCase;

class TranslationsTypeTest extends TranslationsTypeTestCase
{
    public function testSubmitValidDefaultConfigurationData()
    {
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $formType = 'Symfony\Component\Form\Extension\Core\Type\FormType';
            $translationsType = 'A2lix\TranslationFormBundle\Form\Type\TranslationsType';
            $submitType = 'Symfony\Component\Form\Extension\Core\Type\SubmitType';
        } else {
            $formType = 'form';
            $translationsType = 'a2lix_translations';
            $submitType = 'submit';
        }

        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'fr' => [
                    'title' => 'title fr',
                    'description' => 'desc fr',
                ],
                'en' => [
                    'title' => 'title en',
                    'description' => 'desc en',
                ],
                'de' => [
                    'title' => 'title de',
                    'description' => 'desc de',
                ],
            ],
        ];

        $productTranslationFr = new ProductTranslation();
        $productTranslationFr->setLocale('fr')
                             ->setTitle('title fr')
                             ->setDescription('desc fr');
        $productTranslationEn = new ProductTranslation();
        $productTranslationEn->setLocale('en')
                             ->setTitle('title en')
                             ->setDescription('desc en');
        $productTranslationDe = new ProductTranslation();
        $productTranslationDe->setLocale('de')
                             ->setTitle('title de')
                             ->setDescription('desc de');

        $product = new Product();
        $product->setUrl('a2lix.fr')
                ->addTranslation($productTranslationFr)
                ->addTranslation($productTranslationEn)
                ->addTranslation($productTranslationDe);

        //
        // Creation
        //
        $form = $this->factory->createBuilder($formType, new Product())
            ->add('url')
            ->add('translations', $translationsType)
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        //
        // Edition: Modify values
        //
        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'fr' => [
                    'title' => 'title frrrrrr',
                    'description' => 'desc fr',
                ],
                'en' => [
                    'title' => 'title en',
                    'description' => 'desc ennnnnnn',
                ],
                'de' => [
                    'title' => 'title de',
                    'description' => 'desc de',
                ],
            ],
        ];
        $product->getTranslations()['fr']->setTitle('title frrrrrr');
        $product->getTranslations()['en']->setTitle('desc ennnnnnn');

        $form = $this->factory->createBuilder($formType, $product)
            ->add('url')
            ->add('translations', $translationsType)
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());
    }

    public function testSubmitValidConfiguration1Data()
    {
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $formType = 'Symfony\Component\Form\Extension\Core\Type\FormType';
            $translationsType = 'A2lix\TranslationFormBundle\Form\Type\TranslationsType';
            $submitType = 'Symfony\Component\Form\Extension\Core\Type\SubmitType';
        } else {
            $formType = 'form';
            $translationsType = 'a2lix_translations';
            $submitType = 'submit';
        }

        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'es' => [
                    'title' => 'title es',
                    'description' => 'desc es',
                ],
                'fr' => [
                    'title' => 'title fr',
                    'description' => 'desc fr',
                ],
            ],
        ];

        $productTranslationEs = new ProductTranslation();
        $productTranslationEs->setLocale('es')
                             ->setTitle('title es')
                             ->setDescription('desc es');
        $productTranslationFr = new ProductTranslation();
        $productTranslationFr->setLocale('fr')
                             ->setTitle('title fr')
                             ->setDescription('desc fr');

        $product = new Product();
        $product->setUrl('a2lix.fr')
                ->addTranslation($productTranslationEs)
                ->addTranslation($productTranslationFr);

        //
        // Creation
        //
        $form = $this->factory->createBuilder($formType, new Product())
            ->add('url')
            ->add('translations', $translationsType, [
                'locales' => ['es', 'fr', 'de'],
            ])
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        //
        // Edition: Add 'de' locale
        //
        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'es' => [
                    'title' => 'title es',
                    'description' => 'desc es',
                ],
                'fr' => [
                    'title' => 'title fr',
                    'description' => 'desc fr',
                ],
                'de' => [
                    'title' => 'title de',
                    'description' => 'desc de',
                ],
            ],
        ];
        $productTranslationDe = new ProductTranslation();
        $productTranslationDe->setLocale('de')
                             ->setTitle('title de')
                             ->setDescription('desc de');
        $product->addTranslation($productTranslationDe);

        $form = $this->factory->createBuilder($formType, $product)
            ->add('url')
            ->add('translations', $translationsType, [
                'locales' => ['es', 'fr', 'de'],
            ])
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());
    }

    public function testSubmitValidConfiguration2Data()
    {
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $formType = 'Symfony\Component\Form\Extension\Core\Type\FormType';
            $translationsType = 'A2lix\TranslationFormBundle\Form\Type\TranslationsType';
            $submitType = 'Symfony\Component\Form\Extension\Core\Type\SubmitType';
        } else {
            $formType = 'form';
            $translationsType = 'a2lix_translations';
            $submitType = 'submit';
        }

        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'fr' => [
                    'title' => 'title fr',
                ],
                'en' => [
                    'title' => 'title en',
                ],
                'de' => [
                    'title' => 'title de',
                ],
            ],
        ];

        $productTranslationFr = new ProductTranslation();
        $productTranslationFr->setLocale('fr')
                             ->setTitle('title fr');
        $productTranslationEn = new ProductTranslation();
        $productTranslationEn->setLocale('en')
                             ->setTitle('title en');
        $productTranslationDe = new ProductTranslation();
        $productTranslationDe->setLocale('de')
                             ->setTitle('title de');

        $product = new Product();
        $product->setUrl('a2lix.fr')
                ->addTranslation($productTranslationFr)
                ->addTranslation($productTranslationEn)
                ->addTranslation($productTranslationDe);

        //
        // Creation
        //
        $form = $this->factory->createBuilder($formType, new Product())
            ->add('url')
            ->add('translations', $translationsType, [
                'fields' => [
                    'title' => [
                        'label' => 'name',
                    ],
                    'description' => [
                        'display' => false,
                    ],
                ],
            ])
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        //
        // Edition: Add field
        //
        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'fr' => [
                    'title' => 'title fr',
                    'description' => 'desc fr',
                ],
                'en' => [
                    'title' => 'title en',
                    'description' => 'desc en',
                ],
                'de' => [
                    'title' => 'title de',
                    'description' => 'desc de',
                ],
            ],
        ];
        $product->getTranslations()['fr']->setDescription('desc fr');
        $product->getTranslations()['en']->setDescription('desc en');
        $product->getTranslations()['de']->setDescription('desc de');

        $form = $this->factory->createBuilder($formType, $product)
            ->add('url')
            ->add('translations', $translationsType, [
                'fields' => [
                    'title' => [
                        'label' => 'name',
                    ],
                ],
            ])
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());
    }

    protected function getUsedEntityFixtures()
    {
        return [
            'A2lix\\TranslationFormBundle\\Tests\\Gedmo\\Fixtures\\Entity\\Product',
            'A2lix\\TranslationFormBundle\\Tests\\Gedmo\\Fixtures\\Entity\\ProductTranslation',
        ];
    }
}
