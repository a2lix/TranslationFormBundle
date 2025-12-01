<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Twig\Components;

use A2lix\TranslationFormBundle\LocaleProvider\LocaleProviderInterface;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Translation\LocaleSwitcher as TranslationLocaleSwitcher;

class LocaleSwitcher
{
    public string $render = 'nameOnly';
    public string $template = 'native';

    public function __construct(
        private readonly LocaleProviderInterface $localeProvider,
        private readonly TranslationLocaleSwitcher $localeSwitcher,
    ) {}

    public function getLocalesDetails(): iterable
    {
        yield from match ($this->render) {
            'nameAndFlag' => $this->yieldLocalesDetails(withName: true, withFlag: true),
            'nameOnly' => $this->yieldLocalesDetails(withName: true),
            'flagOnly' => $this->yieldLocalesDetails(withFlag: true),
            'codeOnly' => $this->yieldLocalesDetails(),
        };
    }

    private function yieldLocalesDetails(bool $withName = false, bool $withFlag = false): iterable
    {
        $currLocale = $this->localeSwitcher->getLocale();

        yield $this->yieldLocaleDetails($currLocale, $withName, $withFlag);

        foreach ($this->localeProvider->getLocales() as $locale) {
            if ($locale === $currLocale) {
                continue;
            }

            yield $this->yieldLocaleDetails($locale, $withName, $withFlag, $currLocale);
        }
    }

    private function yieldLocaleDetails(
        string $locale,
        bool $withName,
        bool $withFlag,
        ?string $displayLocale = null,
    ): array {
        try {
            $name = $withName ? Locales::getName($locale, $displayLocale) : null;
        } catch (MissingResourceException) {
        }

        return [
            'code' => $locale,
            'name' => $name,
            'flag' => $withFlag ? $this->localeToFlag($locale) : null,
        ];
    }

    private function localeToFlag(string $code): string
    {
        $code = strtoupper($code);
        $offset = 0x1_F1_E6;
        $first = mb_ord($code[0], 'UTF-8') - \ord('A') + $offset;
        $second = mb_ord($code[1], 'UTF-8') - \ord('A') + $offset;

        return mb_chr($first, 'UTF-8').mb_chr($second, 'UTF-8');
    }
}
