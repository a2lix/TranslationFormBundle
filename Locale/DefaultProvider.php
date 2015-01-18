<?php
/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @date 07/11/14
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Locale;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class DefaultProvider implements LocaleProviderInterface
{
    /**
     * Locales
     *
     * @var array
     */
    protected $locales;

    /**
     * Default locale
     *
     * @var
     */
    protected $defaultLocale;

    /**
     * Required locales
     *
     * @var array
     */
    protected $requiredLocales;

    /**
     * @param array $locales
     * @param       $defaultLocale
     * @param array $requiredLocales
     */
    public function __construct(array $locales, $defaultLocale, array $requiredLocales = array())
    {
        if (!in_array($defaultLocale, $locales)) {
            if (count($locales) > 0) {
                throw new \InvalidArgumentException(sprintf('Default locale `%s` not found within the configured locales `[%s]`. Perhaps you need to add it to your `a2lix_translation_form.locales` bundle configuration?', $defaultLocale, implode(',', $locales)));
            }

            throw new \InvalidArgumentException(sprintf('No locales were configured, but expected at least the default locale `%s`. Perhaps you need to add it to your `a2lix_translation_form.locales` bundle configuration?', $defaultLocale));
        }

        if (array_diff($requiredLocales, $locales)) {
            throw new \InvalidArgumentException('Required locales should be contained in locales');
        }

        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
        $this->requiredLocales = $requiredLocales;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getRequiredLocales()
    {
        return $this->requiredLocales;
    }
}
