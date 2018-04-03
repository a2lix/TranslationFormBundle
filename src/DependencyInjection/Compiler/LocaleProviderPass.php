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

namespace A2lix\TranslationFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LocaleProviderPass implements CompilerPassInterface
{
    public const DEFAULT_LOCALE_PROVIDER_KEY = 'default';

    public function process(ContainerBuilder $container): void
    {
        $localeProvider = $container->getParameter('a2lix_translation_form.locale_provider');

        if (self::DEFAULT_LOCALE_PROVIDER_KEY !== $localeProvider) {
            $container->setAlias('a2lix_translation_form.locale_provider.default', $localeProvider);

            return;
        }

        $definition = $container->getDefinition('a2lix_translation_form.locale.simple_provider');
        $definition->setArguments([
            $container->getParameter('a2lix_translation_form.locales'),
            $container->getParameter('a2lix_translation_form.default_locale'),
            $container->getParameter('a2lix_translation_form.required_locales'),
        ]);

        $container->setAlias('a2lix_translation_form.locale_provider.default', 'a2lix_translation_form.locale.simple_provider');
    }
}
