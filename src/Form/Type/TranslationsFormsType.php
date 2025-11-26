<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsFormsType extends AbstractType
{
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('form_options', []);
        $resolver->setAllowedTypes('form_options', 'array');

        $resolver->setRequired('form_type');
        $resolver->setAllowedTypes('form_type', 'string');
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['locales'] as $locale) {
            $builder->add($locale, $options['form_type'], [
                ...$options['form_options'],
                // LocaleExtension options process
                'label' => $formOptions['locale_labels'][$locale] ?? null,
                'required' => \in_array($locale, $options['required_locales'], true),
                'block_name' => ('field' === $options['theming_granularity']) ? 'locale' : null,
            ]);
        }
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'a2lix_translationsForms';
    }
}
