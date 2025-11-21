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
use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use stdClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsFormsType extends AbstractType
{
    public function __construct(
        private readonly LocaleProviderInterface $localeProvider,
    ) {}

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['locales'] as $locale) {
            $builder->add($locale, $options['form_type'], [
                ...$options['form_options'],
                'required' => \in_array($locale, $options['required_locales'], true),
            ]);
        }
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['default_locale'] = $options['default_locale'];
        $view->vars['required_locales'] = $options['required_locales'];
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'locales' => $this->localeProvider->getLocales(),
            'default_locale' => $this->localeProvider->getDefaultLocale(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            'form_options' => [],
            'prototype' => false,
            // 'is_empty_callback' => function (FormInterface $form) {
            //     return false;
            // },
        ]);

        $resolver->setRequired('form_type');
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'a2lix_translationsForms';
    }
}
