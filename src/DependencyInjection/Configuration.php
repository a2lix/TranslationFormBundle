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

namespace A2lix\TranslationFormBundle\DependencyInjection;

use A2lix\TranslationFormBundle\DependencyInjection\Compiler\LocaleProviderPass;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('a2lix_translation_form');

        $rootNode
            ->children()
                ->scalarNode('locale_provider')
                    ->defaultValue(LocaleProviderPass::DEFAULT_LOCALE_PROVIDER_KEY)
                    ->info('Set your own LocaleProvider service identifier if required')
                ->end()
                ->scalarNode('default_locale')
                    ->defaultNull()
                    ->info('Set your own default locale if different from the SymfonyFramework locale. eg: en')
                ->end()
                ->arrayNode('locales')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) {
                            return preg_split('/\s*,\s*/', $v);
                        })
                    ->end()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                    ->info('Set the list of locales to manage (default locale included). eg: [en, fr, de, es]')
                ->end()
                ->arrayNode('required_locales')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) {
                            return preg_split('/\s*,\s*/', $v);
                        })
                    ->end()
                    ->prototype('scalar')->end()
                    ->info('Set the list of required locales to manage. eg: [en]')
                ->end()
                ->scalarNode('templating')
                    ->defaultValue('@A2lixTranslationForm/bootstrap_4_layout.html.twig')
                    ->info('Set your own template path if required')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
