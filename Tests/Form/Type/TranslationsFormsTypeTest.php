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

use A2lix\TranslationFormBundle\Tests\Form\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;

class TranslationsFormsTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $translationsFormsType = $this->getConfiguredTranslationsFormsType(['en', 'fr', 'de'], 'en', ['en', 'fr']);
        $autoFormType = $this->getConfiguredAutoFormType();

        return [new PreloadedExtension([
            $translationsFormsType,
            $autoFormType,
        ], [])];
    }
}
