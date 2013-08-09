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
        $container->setAlias('a2lix_translation_form.manager_registry', $config['manager_registry']);
        $container->setParameter('a2lix_translation_form.templating', $config['templating']);

        // Enable gedmo?
        if ($container->hasParameter('stof_doctrine_extensions.default_locale')) {
            $loader->load('gedmo.xml');

            // If persistDefaultTranslation enabled, detect GedmoTranslation annotations is useless
            if ($container->getParameter('stof_doctrine_extensions.persist_default_translation')) {
                $container->removeDefinition('a2lix_translation_form.listener.controller');
            }
        }
    }
}
