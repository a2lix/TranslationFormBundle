<?php

namespace A2lix\TranslationFormBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader;

/**
 * @author David ALLIX
 */
class A2lixTranslationFormExtension extends Extension
{
    /**
     *
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('a2lix_translation_form.locales', $config['locales']);
        $container->setParameter('a2lix_translation_form.default_required', $config['default_required']);
        $container->setParameter('a2lix_translation_form.templating', $config['templating']);
        $container->setParameter('a2lix_translation_form.default_class.service', $config['default_class']['service']);
        $container->setParameter('a2lix_translation_form.default_class.listener', $config['default_class']['listener']);
        $container->setParameter('a2lix_translation_form.default_class.types.translations', $config['default_class']['types']['translations']);
        $container->setParameter('a2lix_translation_form.default_class.types.translationsFields', $config['default_class']['types']['translationsFields']);

        // Alias to wanted doctrine manager registry
        $container->setAlias('a2lix_translation_form.manager_registry', $config['manager_registry']);

        // Enable gedmo?
        if ($container->hasParameter('stof_doctrine_extensions.default_locale')) {
            $loader->load('gedmo.xml');
        }

        // Use AOP ?
        if ($config['use_aop']) {
            $loader->load('aop.xml');
        }
    }
}
