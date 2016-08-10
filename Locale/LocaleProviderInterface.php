<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Locale;

interface LocaleProviderInterface
{
    /**
     * Get array of locales.
     *
     * @return array
     */
    public function getLocales();

    /**
     * Get default locale.
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Get required locales.
     *
     * @return array
     */
    public function getRequiredLocales();
}
