<?php

declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\DependencyInjection;

use A2lix\TranslationFormBundle\DependencyInjection\A2lixTranslationFormExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class A2lixTranslationFormExtensionTest extends AbstractExtensionTestCase
{
    public function testAfterLoadingParametersAreSet(): void
    {
        $this->load();
        $this->assertContainerBuilderHasParameter('a2lix_translation_form.locale_provider', 'default');
        $this->assertContainerBuilderHasParameter('a2lix_translation_form.locales', ['es', 'en']);
        $this->assertContainerBuilderHasParameter('a2lix_translation_form.required_locales', []);
        $this->assertContainerBuilderHasParameter('a2lix_translation_form.default_locale', 'es');
        $this->assertContainerBuilderHasParameter(
            'a2lix_translation_form.templating',
            '@A2lixTranslationForm/bootstrap_4_layout.html.twig'
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new A2lixTranslationFormExtension(),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'locales' => ['es', 'en'],
            'default_locale' => 'es',
        ];
    }
}
