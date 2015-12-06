<?php

namespace A2lix\TranslationFormBundle\Tests\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType as A2lixTranslationsType;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\Product;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\ProductTranslation;
use A2lix\TranslationFormBundle\Tests\Form\TypeTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\PreloadedExtension;

/**
 * @author David ALLIX
 */
class TranslationsTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $translationsType = $this->getConfiguredTranslationsType(['en', 'fr', 'de'], 'en', ['en', 'fr']);
        $AutoFormType = $this->getConfiguredAutoFormType();

        return [new PreloadedExtension([
            $translationsType,
            $AutoFormType,
        ], [])];
    }

    public function testSubmitValidDefaultConfigurationData()
    {
        // Creation
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

        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('translations', A2lixTranslationsType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        // Edition
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
            ->add('translations', A2lixTranslationsType::class)
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
