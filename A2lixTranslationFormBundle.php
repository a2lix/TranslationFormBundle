<?php

namespace A2lix\TranslationFormBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use A2lix\TranslationFormBundle\DependencyInjection\Compiler\TemplatingPass;

/**
 * @author David ALLIX
 */
class A2lixTranslationFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TemplatingPass());
    }
}