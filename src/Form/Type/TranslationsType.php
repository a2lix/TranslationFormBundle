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

use A2lix\AutoFormBundle\Form\Type\AutoType;
use A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsType extends AbstractType
{
    public function __construct(
        private readonly TranslationsListener $translationsListener,
    ) {}

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_class' => null,
            // AutoType options
            'children' => [],
            'children_excluded' => [],
            'children_embedded' => [],
            // 'children_translated' => true,
            'children_groups' => [],
            'builder' => null,
        ]);
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $validAutoTypeOptions = $options;
        unset(
            // TranslationsType options
            $validAutoTypeOptions['translation_class'],
            // LocaleExtension options
            $validAutoTypeOptions['default_locale'],
            $validAutoTypeOptions['required_locales'],
            $validAutoTypeOptions['locales'],
            $validAutoTypeOptions['locale_labels'],
            $validAutoTypeOptions['theming_granularity'],
        );

        // Build once only
        $autoChildren = $builder->create('tmp', AutoType::class, [
            'data_class' => $options['translation_class'],
        ])->all();

        foreach ($options['locales'] as $locale) {
            $localeFormBuilder = $builder->create($locale, FormType::class, [
                'data_class' => $options['translation_class'],
                // LocaleExtension options process
                'label' => $formOptions['locale_labels'][$locale] ?? null,
                'required' => \in_array($locale, $options['required_locales'], true),
                'block_name' => ('field' === $options['theming_granularity']) ? 'locale' : null,
            ]);

            foreach ($autoChildren as $autoChild) {
                $localeFormBuilder->add($autoChild);
            }

            $builder->add($localeFormBuilder);
        }

        $builder->addEventSubscriber($this->translationsListener);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'a2lix_translations';
    }
}
