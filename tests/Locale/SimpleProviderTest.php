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

namespace A2lix\TranslationFormBundle\Tests\Locale;

use A2lix\TranslationFormBundle\Locale\SimpleProvider;
use PHPUnit\Framework\TestCase;

class SimpleProviderTest extends TestCase
{
    protected $locales;
    protected $defaultLocale;
    protected $requiredLocales;
    protected $provider;

    public function setUp(): void
    {
        $this->locales = ['es', 'en', 'pt'];
        $this->defaultLocale = 'en';
        $this->requiredLocales = ['es', 'en'];

        $this->provider = new SimpleProvider($this->locales, $this->defaultLocale, $this->requiredLocales);
    }

    public function testDefaultLocaleIsInLocales(): void
    {
        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder(SimpleProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set expectations for constructor calls
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Default locale `de` not found within the configured locales `[es,en]`.'
            .' Perhaps you need to add it to your `a2lix_translation_form.locales` bundle configuration?');

        // Now call the constructor
        $reflectedClass = new \ReflectionClass(SimpleProvider::class);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, ['es', 'en'], 'de', []);
    }

    public function testRequiredLocaleAreInLocales(): void
    {
        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder(SimpleProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set expectations for constructor calls
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Required locales should be contained in locales');

        // Now call the constructor
        $reflectedClass = new \ReflectionClass(SimpleProvider::class);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, ['es', 'en'], 'en', ['en', 'pt']);
    }

    public function testGetLocales(): void
    {
        $expected = $this->provider->getLocales();
        $locales = $this->locales;

        $this->assertSame(array_diff($expected, $locales), array_diff($locales, $expected));
    }

    public function testGetDefaultLocale(): void
    {
        $expected = $this->provider->getDefaultLocale();

        $this->assertSame($this->defaultLocale, $expected);
    }

    public function getRequiredLocales(): void
    {
        $expected = $this->provider->getDefaultLocale();
        $requiredLocales = $this->requiredLocales;

        $this->assertSame(array_diff($expected, $requiredLocales), array_diff($requiredLocales, $expected));
    }
}
