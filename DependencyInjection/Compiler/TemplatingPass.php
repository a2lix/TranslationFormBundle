<?php

namespace A2lix\TranslationFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder;

class TemplatingPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false !== ($template = $container->getParameter('a2lix_translation_form.templating'))) {
            $resources = $container->getParameter('twig.form.resources');

            if (!in_array($template, $resources)) {
                $resources[] = $template;
                $container->setParameter('twig.form.resources', $resources);
            }
        }
    }
}
