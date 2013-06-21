<?php

namespace A2lix\TranslationFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author David ALLIX
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('a2lix_translation_form');

        $rootNode
            ->append($this->getDefaultClassNode())
            ->children()
                ->arrayNode('locales')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function($v) { return preg_split('/\s*,\s*/', $v); })
                    ->end()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->booleanNode('default_required')->defaultTrue()->end()
                ->booleanNode('use_aop')->defaultFalse()->end()
                ->scalarNode('templating')->defaultValue("A2lixTranslationFormBundle::default.html.twig")->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function getDefaultClassNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('default_class');

        $node
            ->append($this->getDefaultTypesClassNode())
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('service')
                    ->cannotBeEmpty()
                    ->defaultValue('A2lix\TranslationFormBundle\TranslationForm\DefaultTranslationForm')
                ->end()
            ->end()
            ->children()
                ->scalarNode('listener')
                    ->cannotBeEmpty()
                    ->defaultValue('A2lix\TranslationFormBundle\Form\EventListener\DefaultTranslationsSubscriber')
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getDefaultTypesClassNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('types');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('translations')
                    ->cannotBeEmpty()
                    ->defaultValue('A2lix\TranslationFormBundle\Form\Type\TranslationsType')
                ->end()
                ->scalarNode('translationsFields')
                    ->cannotBeEmpty()
                    ->defaultValue('A2lix\TranslationFormBundle\Form\Type\TranslationsFieldsType')
                ->end()
            ->end()
        ;

        return $node;
    }
}
