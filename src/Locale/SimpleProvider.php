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

namespace A2lix\TranslationFormBundle\Locale;

class SimpleProvider implements LocaleProviderInterface
{
    /** @var array */
    protected $locales;
    /** @var string */
    protected $defaultLocale;
    /** @var array */
    protected $requiredLocales;

    public function __construct(array $locales, string $defaultLocale, array $requiredLocales = [])
    {
        if (!in_array($defaultLocale, $locales, true)) {
            if (count($locales)) {
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

    public function getLocales(): array
    {
        return $this->locales;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    public function getRequiredLocales(): array
    {
        return $this->requiredLocales;
    }
}
