<?php

/*
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle;

use A2lix\TranslationFormBundle\DependencyInjection\Compiler\LocaleProviderPass;
use A2lix\TranslationFormBundle\DependencyInjection\Compiler\TemplatingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author David ALLIX
 */
class A2lixTranslationFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TemplatingPass());
        $container->addCompilerPass(new LocaleProviderPass());
    }
}
