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

namespace A2lix\TranslationFormBundle;

use A2lix\TranslationFormBundle\DependencyInjection\Compiler\LocaleProviderPass;
use A2lix\TranslationFormBundle\DependencyInjection\Compiler\TemplatingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class A2lixTranslationFormBundle extends Bundle
{
    #[\Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        /** @psalm-suppress UndefinedMethod */
        /** @psalm-suppress MixedMethodCall */
        $definition->rootNode()

        // TODO
        ;


        // $treeBuilder = new TreeBuilder('a2lix_translation_form');
        // $rootNode = method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('a2lix_translation_form');

        // $rootNode
        //     ->children()
        //     ->scalarNode('locale_provider')
        //     ->defaultValue(LocaleProviderPass::DEFAULT_LOCALE_PROVIDER_KEY)
        //     ->info('Set your own LocaleProvider service identifier if required')
        //     ->end()
        //     ->scalarNode('default_locale')
        //     ->defaultNull()
        //     ->info('Set your own default locale if different from the SymfonyFramework locale. eg: en')
        //     ->end()
        //     ->arrayNode('locales')
        //     ->beforeNormalization()
        //     ->ifString()
        //     ->then(static fn ($v) => preg_split('/\s*,\s*/', (string) $v))
        //     ->end()
        //     ->requiresAtLeastOneElement()
        //     ->prototype('scalar')->end()
        //     ->info('Set the list of locales to manage (default locale included). eg: [en, fr, de, es]')
        //     ->end()
        //     ->arrayNode('required_locales')
        //     ->beforeNormalization()
        //     ->ifString()
        //     ->then(static fn ($v) => preg_split('/\s*,\s*/', (string) $v))
        //     ->end()
        //     ->prototype('scalar')->end()
        //     ->info('Set the list of required locales to manage. eg: [en]')
        //     ->end()
        //     ->scalarNode('templating')
        //     ->defaultValue('@A2lixTranslationForm/bootstrap_4_layout.html.twig')
        //     ->info('Set your own template path if required')
        //     ->end()
        //     ->end()
        // ;

        // return $treeBuilder;
    }

    #[\Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $container->services()

        // $container->setParameter('a2lix_translation_form.locale_provider', $config['locale_provider']);
        // $container->setParameter('a2lix_translation_form.locales', $config['locales']);
        // $container->setParameter('a2lix_translation_form.required_locales', $config['required_locales']);
        // $container->setParameter('a2lix_translation_form.default_locale', $config['default_locale'] ?:
        //     $container->getParameter('kernel.default_locale'));

        // $container->setParameter('a2lix_translation_form.templating', $config['templating']);
    }

    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TemplatingPass());
        $container->addCompilerPass(new LocaleProviderPass());
    }
}
