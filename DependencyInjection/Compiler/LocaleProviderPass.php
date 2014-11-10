<?php
/**
 * @author    Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @date      07/11/14
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $localeProvider = $container->getParameter('a2lix_translation_form.locale_provider');

        if ('default' === $localeProvider) {
            $container->setAlias('a2lix_translation_form.default.service.locale_provider', 'a2lix_translation_form.default.service.parameter_locale_provider');

            $definition = $container->getDefinition('a2lix_translation_form.default.service.parameter_locale_provider');

            $definition->setArguments(array(
                $container->getParameter('a2lix_translation_form.locales'),
                $container->getParameter('a2lix_translation_form.default_locale'),
                $container->getParameter('a2lix_translation_form.required_locales')
            ));
            
        } else {
            $container->setAlias('a2lix_translation_form.default.service.locale_provider', $localeProvider);
        }
    }
}