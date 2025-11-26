<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class A2lixTranslationFormBundle extends AbstractBundle
{
    #[\Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('locale_provider')
            ->defaultValue('a2lix_translation_form.locale.simple_provider')
            ->info('Set your own LocaleProvider service identifier if required')
            ->end()
            ->scalarNode('default_locale')
            ->defaultNull()
            ->info('Set your own default locale if different from the default kernel.default_locale. eg: en')
            ->end()
            ->arrayNode('locales')
            ->beforeNormalization()
            ->ifString()
            ->then(static fn ($v) => preg_split('/\s*,\s*/', (string) $v))
            ->end()
            ->requiresAtLeastOneElement()
            ->prototype('scalar')->end()
            ->info('Set the list of locales to manage (default locale included). eg: [en, fr, de, es]')
            ->end()
            ->arrayNode('required_locales')
            ->beforeNormalization()
            ->ifString()
            ->then(static fn ($v) => preg_split('/\s*,\s*/', (string) $v))
            ->end()
            ->prototype('scalar')->end()
            ->info('Set the list of required locales to manage. eg: [en]')
            ->end()
            ->scalarNode('templating')
            ->defaultValue('@A2lixTranslationForm/native_layout.html.twig')
            ->info('Set your own template path if required')
            ->end()
            ->end()
        ;
    }

    #[\Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        // Locale Provider
        if ('a2lix_translation_form.locale.simple_provider' !== $config['locale_provider']) {
            $container->services()
                ->remove('a2lix_translation_form.locale.simple_provider')
                ->alias('a2lix_translation_form.locale_provider.default', $config['locale_provider'])
            ;
        } else {
            $container->services()
                ->get($config['locale_provider'])
                ->args([
                    '$locales' => $config['locales'],
                    '$defaultLocale' => $config['default_locale'] ?? $builder->getParameter('kernel.default_locale'),
                    '$requiredLocales' => $config['required_locales'],
                ])
                ->alias('a2lix_translation_form.locale_provider.default', $config['locale_provider'])
            ;
        }
    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig($this->extensionAlias);

        if ($container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', [
                'form_themes' => [
                    $config['templating'] ?? '@A2lixTranslationForm/native_layout.html.twig',
                ],
            ]);
        }

        $container->prependExtensionConfig('a2lix_auto_form', [
            'children_excluded' => [
                'id', 'newTranslations', 'translatable', 'locale', 'currentLocale', 'defaultLocale',
            ],
        ]);
    }
}
