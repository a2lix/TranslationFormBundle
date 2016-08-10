<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 * (c) Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('a2lix_translation_form');

        $rootNode
            ->children()
                ->scalarNode('locale_provider')->defaultValue('default')->end()
                ->scalarNode('default_locale')->defaultNull()->end()
                ->arrayNode('locales')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) {
                            return preg_split('/\s*,\s*/', $v);
                        })
                    ->end()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('required_locales')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function ($v) {
                            return preg_split('/\s*,\s*/', $v);
                        })
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('templating')->defaultValue('A2lixTranslationFormBundle::default.html.twig')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
