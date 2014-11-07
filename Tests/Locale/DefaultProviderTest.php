<?php
/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @date 07/11/14
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Locale;

use A2lix\TranslationFormBundle\Locale\DefaultProvider;


/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class DefaultProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;
    protected $locales;
    protected $defaultLocale;
    protected $requiredLocales;

    public function setUp()
    {
        $this->locales = array('es', 'en', 'pt');
        $this->defaultLocale = 'en';
        $this->requiredLocales = array('es', 'en');

        $this->provider = new DefaultProvider($this->locales, $this->defaultLocale, $this->requiredLocales);
    }

    public function testDefaultLocaleIsInLocales()
    {
        $classname = 'A2lix\TranslationFormBundle\Locale\DefaultProvider';

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->getMock();

        // set expectations for constructor calls
        $this->setExpectedException(
            'InvalidArgumentException', 'Default locale should be contained in locales'
        );

        // now call the constructor
        $reflectedClass = new \ReflectionClass($classname);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, array('es', 'en'), 'de', array());
    }

    public function testRequiredLocaleAreInLocales()
    {
        $classname = 'A2lix\TranslationFormBundle\Locale\DefaultProvider';

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->getMock();

        // set expectations for constructor calls
        $this->setExpectedException(
            'InvalidArgumentException', 'Required locales should be contained in locales'
        );

        // now call the constructor
        $reflectedClass = new \ReflectionClass($classname);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, array('es', 'en'), 'en', array('en', 'pt'));
    }

    public function testGetLocales()
    {
        $expected = $this->provider->getLocales();
        $locales = $this->locales;

        $this->assertSame(array_diff($expected, $locales), array_diff($locales, $expected));
    }

    public function testGetDefaultLocale()
    {
        $expected = $this->provider->getDefaultLocale();

        $this->assertEquals($this->defaultLocale, $expected);
    }

    public function getRequiredLocales()
    {
        $expected = $this->provider->getDefaultLocale();
        $requiredLocales = $this->requiredLocales;

        $this->assertSame(array_diff($expected, $requiredLocales), array_diff($requiredLocales, $expected));
    }
} 