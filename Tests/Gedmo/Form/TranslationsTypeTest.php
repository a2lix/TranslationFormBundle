<?php

namespace A2lix\TranslationFormBundle\Tests\Gedmo\Form;

use A2lix\TranslationFormBundle\Tests\Gedmo\Fixtures\Entity\Product;
use A2lix\TranslationFormBundle\Tests\Gedmo\Fixtures\Entity\ProductTranslation;
use A2lix\TranslationFormBundle\Tests\TranslationsTypeTestCase;

class TranslationsTypeTest extends TranslationsTypeTestCase
{
    protected function getUsedEntityFixtures()
    {
        return array(
            'A2lix\\TranslationFormBundle\\Tests\\Gedmo\\Fixtures\\Entity\\Product',
            'A2lix\\TranslationFormBundle\\Tests\\Gedmo\\Fixtures\\Entity\\ProductTranslation',
        );
    }

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

        $formData = array(
            'url' => 'a2lix.fr',
            'translations' => array(
                'fr' => array(
                    'title' => 'title fr',
                    'description' => 'desc fr'
                ),
                'en' => array(
                    'title' => 'title en',
                    'description' => 'desc en'
                ),
                'de' => array(
                    'title' => 'title de',
                    'description' => 'desc de'
                )
            ),
        );

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
        $formData = array(
            'url' => 'a2lix.fr',
            'translations' => array(
                'fr' => array(
                    'title' => 'title frrrrrr',
                    'description' => 'desc fr'
                ),
                'en' => array(
                    'title' => 'title en',
                    'description' => 'desc ennnnnnn'
                ),
                'de' => array(
                    'title' => 'title de',
                    'description' => 'desc de'
                )
            ),
        );
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



//        $view = $form->createView();
//        $children = $view->children;
//
//        foreach (array_keys($formData) as $key) {
//            $this->assertArrayHasKey($key, $children);
//        }
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

        $formData = array(
            'url' => 'a2lix.fr',
            'translations' => array(
                'es' => array(
                    'title' => 'title es',
                    'description' => 'desc es'
                ),
                'fr' => array(
                    'title' => 'title fr',
                    'description' => 'desc fr'
                )
            ),
        );

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
            ->add('translations', $translationsType, array(
                'locales' => array('es', 'fr', 'de')
            ))
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        //
        // Edition: Add 'de' locale
        //
        $formData = array(
            'url' => 'a2lix.fr',
            'translations' => array(
                'es' => array(
                    'title' => 'title es',
                    'description' => 'desc es'
                ),
                'fr' => array(
                    'title' => 'title fr',
                    'description' => 'desc fr'
                ),
                'de' => array(
                    'title' => 'title de',
                    'description' => 'desc de'
                )
            ),
        );
        $productTranslationDe = new ProductTranslation();
        $productTranslationDe->setLocale('de')
                             ->setTitle('title de')
                             ->setDescription('desc de');
        $product->addTranslation($productTranslationDe);

        $form = $this->factory->createBuilder($formType, $product)
            ->add('url')
            ->add('translations', $translationsType, array(
                'locales' => array('es', 'fr', 'de')
            ))
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());



//        $view = $form->createView();
//        $children = $view->children;
//
//        foreach (array_keys($formData) as $key) {
//            $this->assertArrayHasKey($key, $children);
//        }
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

        $formData = array(
            'url' => 'a2lix.fr',
            'translations' => array(
                'fr' => array(
                    'title' => 'title fr',
                ),
                'en' => array(
                    'title' => 'title en',
                ),
                'de' => array(
                    'title' => 'title de',
                )
            ),
        );

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
            ->add('translations', $translationsType, array(
                'fields' => array(
                    'title' => array(
                        'label' => 'name'
                    ),
                    'description' => array(
                        'display' => false
                    )
                )
            ))
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());

        //
        // Edition: Add field
        //
        $formData = array(
            'url' => 'a2lix.fr',
            'translations' => array(
                'fr' => array(
                    'title' => 'title fr',
                    'description' => 'desc fr'
                ),
                'en' => array(
                    'title' => 'title en',
                    'description' => 'desc en'
                ),
                'de' => array(
                    'title' => 'title de',
                    'description' => 'desc de'
                )
            ),
        );
        $product->getTranslations()['fr']->setDescription('desc fr');
        $product->getTranslations()['en']->setDescription('desc en');
        $product->getTranslations()['de']->setDescription('desc de');

        $form = $this->factory->createBuilder($formType, $product)
            ->add('url')
            ->add('translations', $translationsType, array(
                'fields' => array(
                    'title' => array(
                        'label' => 'name'
                    )
                )
            ))
            ->add('save', $submitType)
            ->getForm();
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($product, $form->getData());



//        $view = $form->createView();
//        $children = $view->children;
//
//        foreach (array_keys($formData) as $key) {
//            $this->assertArrayHasKey($key, $children);
//        }
    }
}
