<?php

namespace A2lix\TranslationFormBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader;

/**
 * @author David ALLIX
 */
class A2lixTranslationFormExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['use_aop']) {
            $loader->load('aop.xml');
        }

        // Use object manager named in config.yml
        $container->setAlias('a2lix_translation_form.object_manager', $config['object_manager']);

        $container->setParameter('a2lix_translation_form.locales', $config['locales']);
        $container->setParameter('a2lix_translation_form.default_required', $config['default_required']);
    }
}
