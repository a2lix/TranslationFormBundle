<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\LocaleProvider;

class SimpleLocaleProvider implements LocaleProviderInterface
{
    /**
     * @param list<string> $locales
     * @param list<string> $requiredLocales
     */
    public function __construct(
        private readonly array $locales,
        private readonly string $defaultLocale,
        private readonly array $requiredLocales = [],
    ) {}

    #[\Override]
    public function getLocales(): array
    {
        /** @var list<string> */
        return $this->locales;
    }

    #[\Override]
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    #[\Override]
    public function getRequiredLocales(): array
    {
        return $this->requiredLocales;
    }
}
