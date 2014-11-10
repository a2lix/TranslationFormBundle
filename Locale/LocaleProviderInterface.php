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
 * Provides the locales
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface LocaleProviderInterface
{
    /**
     * Get array of locales
     *
     * @return array
     */
    public function getLocales();

    /**
     * Get default locale
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Get required locales
     *
     * @return array
     */
    public function getRequiredLocales();
}