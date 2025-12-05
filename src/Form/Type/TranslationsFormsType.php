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

use A2lix\TranslationFormBundle\Helper\OneLocaleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @phpstan-type FormOptionsDefaults array{
 *    default_locale: string,
 *    required_locales: list<string>,
 *    locales: list<string>,
 *    locale_labels: array<string, string>|null,
 *    theming_granularity: string,
 *    form_options: array<string, mixed>,
 *    form_type: string,
 *    ...
 * }
 *
 * @extends AbstractType<mixed>
 */
class TranslationsFormsType extends AbstractType
{
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // FormType
            'by_reference' => false,
            'empty_data' => new ArrayCollection(),
            // Adds
            'form_options' => [],
        ]);

        $resolver->setAllowedTypes('form_options', 'array');
        $resolver->setRequired('form_type');
        $resolver->setAllowedTypes('form_type', 'string');
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FormOptionsDefaults $options */
        $options = $options;

        foreach ($options['locales'] as $locale) {
            $builder->add($locale, $options['form_type'], [
                ...$options['form_options'],
                'setter' => static function (Collection $translationColl, ?OneLocaleInterface $translation, FormInterface $form) use ($locale): void {
                    if (null === $translation) {
                        return;
                    }

                    if ($translation->isEmpty()) {
                        $translationColl->removeElement($translation);

                        return;
                    }

                    $translation->locale = $locale; // @phpstan-ignore property.notFound
                    $translationColl->add($translation);
                },
                // LocaleExtension options process
                'label' => $options['locale_labels'][$locale] ?? null,
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
