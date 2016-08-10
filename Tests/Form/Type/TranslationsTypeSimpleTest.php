<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\Product;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\ProductTranslation;
use A2lix\TranslationFormBundle\Tests\Form\TypeTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\PreloadedExtension;

class TranslationsTypeSimpleTest extends TypeTestCase
{
    protected $locales = ['en', 'fr', 'de'];
    protected $defaultLocale = 'en';
    protected $requiredLocales = ['en', 'fr'];

    protected function getExtensions()
    {
        $translationsType = $this->getConfiguredTranslationsType($this->locales, $this->defaultLocale, $this->requiredLocales);
        $autoFormType = $this->getConfiguredAutoFormType();

        return [new PreloadedExtension([
            $translationsType,
            $autoFormType,
        ], [])];
    }

    public function testEmptyForm()
    {
        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('translations', TranslationsType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $translationsForm = $form->get('translations')->all();
        $translationsLocales = array_keys($translationsForm);
        $translationsRequiredLocales = array_keys(array_filter($translationsForm, function ($form) {
            return $form->isRequired();
        }));

        $this->assertEquals($this->locales, $translationsLocales, 'Locales should be same as config');
        $this->assertEquals($this->requiredLocales, $translationsRequiredLocales, 'Required locales should be same as config');

        $this->assertEquals(['title', 'description'], array_keys($translationsForm['en']->all()), 'Fields should matches ProductTranslation fields');
        $this->assertEquals(['title', 'description'], array_keys($translationsForm['fr']->all()), 'Fields should matches ProductTranslation fields');
        $this->assertEquals(['title', 'description'], array_keys($translationsForm['de']->all()), 'Fields should matches ProductTranslation fields');
    }

    public function testCreationForm()
    {
        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('translations', TranslationsType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $productTranslationEn = new ProductTranslation();
        $productTranslationEn->setLocale('en')
                             ->setTitle('title en')
                             ->setDescription('desc en');
        $productTranslationFr = new ProductTranslation();
        $productTranslationFr->setLocale('fr')
                             ->setTitle('title fr')
                             ->setDescription('desc fr');
        $productTranslationDe = new ProductTranslation();
        $productTranslationDe->setLocale('de')
                             ->setTitle('title de')
                             ->setDescription('desc de');

        $product = new Product();
        $product->setUrl('a2lix.fr')
                ->addTranslation($productTranslationEn)
                ->addTranslation($productTranslationFr)
                ->addTranslation($productTranslationDe);

        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'en' => [
                    'title' => 'title en',
                    'description' => 'desc en',
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

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        return $product;
    }

    /**
     * @depends testCreationForm
     */
    public function testEditionForm($product)
    {
        $product->getTranslations()['en']->setDescription('desc ennnnnnn');
        $product->getTranslations()['fr']->setTitle('title frrrrrr');

        $formData = [
            'url' => 'a2lix.fr',
            'translations' => [
                'en' => [
                    'title' => 'title en',
                    'description' => 'desc ennnnnnn',
                ],
                'fr' => [
                    'title' => 'title frrrrrr',
                    'description' => 'desc fr',
                ],
                'de' => [
                    'title' => 'title de',
                    'description' => 'desc de',
                ],
            ],
        ];

        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('translations', TranslationsType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
