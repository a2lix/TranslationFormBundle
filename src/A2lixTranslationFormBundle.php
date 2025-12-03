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

use A2lix\TranslationFormBundle\LocaleProvider\LocaleProviderInterface;
use A2lix\TranslationFormBundle\LocaleProvider\SimpleLocaleProvider;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Intl\Locales;

class A2lixTranslationFormBundle extends AbstractBundle
{
    #[\Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('locale_provider')
            ->defaultValue(SimpleLocaleProvider::class)
            ->info('Set your own LocaleProvider service identifier if required')
            ->end()
            ->scalarNode('default_locale')
            ->defaultNull()
            ->info('Set your own default locale if different from the default kernel.default_locale. eg: en')
            ->end()
            ->arrayNode('locales')
            ->requiresAtLeastOneElement()
            ->scalarPrototype()
            ->end()
            ->info('Set the list of locales to manage (default locale included). eg: [en, fr, de, es]')
            ->end()
            ->arrayNode('required_locales')
            ->scalarPrototype()
            ->end()
            ->info('Set the list of required locales to manage. eg: [en]')
            ->end()
            ->scalarNode('templating')
            ->defaultValue('@A2lixTranslationForm/native_layout.html.twig')
            ->info('Set your own template path if required')
            ->end()
            ->end()

            ->validate()
            ->ifTrue(static fn (array $v): bool => [] !== array_diff($v['required_locales'], $v['locales']))
            ->thenInvalid('Configuration error in a2lix_translation_form: All required locales must be present in the defined locales.')
            ->end()
        ;
    }

    #[\Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        $this->configureLocaleProvider($config, $container, $builder);
    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $config = $container->getExtensionConfig($this->extensionAlias);

        if ($container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', [
                'form_themes' => [
                    $config['templating'] ?? '@A2lixTranslationForm/bootstrap_5_layout.html.twig',
                ],
            ]);
        }

        $container->prependExtensionConfig('a2lix_auto_form', [
            'children_excluded' => [
                'id',
                'newTranslations',
                'translatable',
                'locale',
                'currentLocale',
                'defaultLocale',
            ],
        ]);
    }

    private function configureLocaleProvider(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // Custom?
        if (SimpleLocaleProvider::class !== $config['locale_provider']) {
            $container->services()
                ->remove(SimpleLocaleProvider::class)
                ->alias(LocaleProviderInterface::class, $config['locale_provider'])
            ;

            return;
        }

        // SimpleProvider
        foreach ($config['locales'] as $locale) {
            if (!Locales::exists($locale)) {
                throw new \InvalidArgumentException(\sprintf('Configuration error in a2lix_translation_form: The locale "%s" is not a valid country code or locale code recognized by the Symfony Intl component.', $locale));
            }
        }

        $defaultLocale = $config['default_locale'] ?? $builder->getParameter('kernel.default_locale');
        if (!\in_array($defaultLocale, $config['locales'], true)) {
            throw new \InvalidArgumentException(\sprintf('Configuration error in a2lix_translation_form: The list of locales must contain the determined default locale "%s"', $defaultLocale));
        }

        $container->services()
            ->get($config['locale_provider'])
            ->args([
                '$locales' => $config['locales'],
                '$defaultLocale' => $defaultLocale,
                '$requiredLocales' => $config['required_locales'],
            ])
        ;
    }
}
