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

class LocaleSwitcher
{
    public string $render = 'basic';

    public function __construct(
        private readonly LocaleProviderInterface $localeProvider,
    ) {}

    public function getLocales(): iterable
    {
        return $this->localeProvider->getLocales();
    }
}
