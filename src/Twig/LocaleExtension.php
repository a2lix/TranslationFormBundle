<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Twig;

use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Component\Intl\Locales;
use Twig\Attribute\AsTwigFunction;
use Twig\Extension\CoreExtension;

final class LocaleExtension
{
    public function __construct(
        private readonly LocaleSwitcher $localeSwitcher,
    ) {}

    #[AsTwigFunction('locale_render', needsCharset: true)]
    public function localeRender(string $charset, string $locale, string $render = 'locale_name_title'): string
    {
        if (str_starts_with($render, 'locale_name_')) {
            $locale = Locales::getName($locale, $this->localeSwitcher->getLocale());
        }

        return match ($render) {
            'locale_upper', 'locale_name_upper' => CoreExtension::upper($charset, $locale),
            'locale_title', 'locale_name_title' => CoreExtension::titleCase($charset, $locale),
        };
    }
}
