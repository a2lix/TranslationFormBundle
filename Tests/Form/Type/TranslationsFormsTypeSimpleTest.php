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

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\MediaLocalize;
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\Product;
use A2lix\TranslationFormBundle\Tests\Fixtures\Form\MediaLocalizeType;
use A2lix\TranslationFormBundle\Tests\Form\TypeTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\PreloadedExtension;

class TranslationsFormsTypeSimpleTest extends TypeTestCase
{
    protected $locales = ['en', 'fr', 'de'];
    protected $defaultLocale = 'en';
    protected $requiredLocales = ['en', 'fr'];

    protected function getExtensions()
    {
        $translationsFormsType = $this->getConfiguredTranslationsFormsType($this->locales, $this->defaultLocale, $this->requiredLocales);
        $autoFormType = $this->getConfiguredAutoFormType();

        return [new PreloadedExtension([
            $translationsFormsType,
            $autoFormType,
        ], [])];
    }

    public function testEmptyForm()
    {
        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('medias', TranslationsFormsType::class, [
                'form_type' => MediaLocalizeType::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm();

        $mediasForm = $form->get('medias')->all();
        $mediasLocales = array_keys($mediasForm);
        $mediasRequiredLocales = array_keys(array_filter($mediasForm, function ($form) {
            return $form->isRequired();
        }));

        $this->assertEquals($this->locales, $mediasLocales, 'Locales should be same as config');
        $this->assertEquals($this->requiredLocales, $mediasRequiredLocales, 'Required locales should be same as config');

        $this->assertEquals(['url', 'description'], array_keys($mediasForm['en']->all()), 'Fields should matches MediaLocalizeType fields');
        $this->assertEquals(['url', 'description'], array_keys($mediasForm['fr']->all()), 'Fields should matches MediaLocalizeType fields');
        $this->assertEquals(['url', 'description'], array_keys($mediasForm['de']->all()), 'Fields should matches MediaLocalizeType fields');
    }

    public function testCreationForm()
    {
        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('medias', TranslationsFormsType::class, [
                'form_type' => MediaLocalizeType::class,
            ])
            ->add('save', SubmitType::class)
            ->getForm();

        $mediaEn = new MediaLocalize();
        $mediaEn->setLocale('en')
                ->setUrl('http://en')
                ->setDescription('desc en');
        $mediaFr = new MediaLocalize();
        $mediaFr->setLocale('fr')
                ->setUrl('http://fr')
                ->setDescription('desc fr');
        $mediaDe = new MediaLocalize();
        $mediaDe->setLocale('de')
                ->setUrl('http://de')
                ->setDescription('desc de');

        $product = new Product();
        $product->setUrl('a2lix.fr')
                ->addMedia($mediaEn)
                ->addMedia($mediaFr)
                ->addMedia($mediaDe);

        $formData = [
            'url' => 'a2lix.fr',
            'medias' => [
                'en' => [
                    'url' => 'http://en',
                    'description' => 'desc en',
                ],
                'fr' => [
                    'url' => 'http://fr',
                    'description' => 'desc fr',
                ],
                'de' => [
                    'url' => 'http://de',
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
        $product->getMedias()['en']->setUrl('http://ennnnn');
        $product->getMedias()['fr']->setDescription('desc frrrrrr');

        $formData = [
            'url' => 'a2lix.fr',
            'medias' => [
                'en' => [
                    'url' => 'http://ennnnn',
                    'description' => 'desc en',
                ],
                'fr' => [
                    'url' => 'http://fr',
                    'description' => 'desc frrrrrr',
                ],
                'de' => [
                    'url' => 'http://de',
                    'description' => 'desc de',
                ],
            ],
        ];

        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('medias', TranslationsFormsType::class, [
                'form_type' => MediaLocalizeType::class,
            ])
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
