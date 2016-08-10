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
use A2lix\TranslationFormBundle\Tests\Fixtures\Entity\Product;
use A2lix\TranslationFormBundle\Tests\Fixtures\Form\MediaLocalizeType;
use A2lix\TranslationFormBundle\Tests\Form\TypeTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\PreloadedExtension;

class TranslationsFormsTypeAdvancedTest extends TypeTestCase
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

    public function testEmptyFormOverrideLocales()
    {
        $overrideLocales = ['en', 'fr', 'es'];
        $overrideRequiredLocales = ['en', 'es'];

        $form = $this->factory->createBuilder(FormType::class, new Product())
            ->add('url')
            ->add('medias', TranslationsFormsType::class, [
                'form_type' => MediaLocalizeType::class,
                'locales' => $overrideLocales,
                'required_locales' => $overrideRequiredLocales,
            ])
            ->add('save', SubmitType::class)
            ->getForm();

        $mediasForm = $form->get('medias')->all();
        $mediasLocales = array_keys($mediasForm);
        $mediasRequiredLocales = array_keys(array_filter($mediasForm, function ($form) {
            return $form->isRequired();
        }));

        $this->assertEquals($overrideLocales, $mediasLocales, 'Locales should be same as config');
        $this->assertEquals($overrideRequiredLocales, $mediasRequiredLocales, 'Required locales should be same as config');

        $this->assertEquals(['url', 'description'], array_keys($mediasForm['en']->all()), 'Fields should matches MediaLocalizeType fields');
        $this->assertEquals(['url', 'description'], array_keys($mediasForm['fr']->all()), 'Fields should matches MediaLocalizeType fields');
        $this->assertEquals(['url', 'description'], array_keys($mediasForm['es']->all()), 'Fields should matches MediaLocalizeType fields');
    }
}
