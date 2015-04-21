<?php

namespace A2lix\TranslationFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author David ALLIX
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class A2lixTranslationFormExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('a2lix_form.xml');

        $container->setParameter('a2lix_translation_form.locale_provider', $config['locale_provider']);
        $container->setParameter('a2lix_translation_form.locales', $config['locales']);
        $container->setParameter('a2lix_translation_form.required_locales', $config['required_locales']);
        $container->setParameter('a2lix_translation_form.default_locale', $config['default_locale'] ?:
        $container->getParameter('kernel.default_locale', 'en'));

        $container->setParameter('a2lix_translation_form.templating', $config['templating']);
    }
}
