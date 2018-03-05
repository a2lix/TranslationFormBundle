<?php

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
