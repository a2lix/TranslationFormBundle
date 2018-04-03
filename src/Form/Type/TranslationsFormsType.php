<?php

declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Type;

use A2lix\AutoFormBundle\Form\Type\AutoFormType;
use A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener;
use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsFormsType extends AbstractType
{
    /** @var TranslationsFormsListener */
    private $translationsFormsListener;
    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(TranslationsFormsListener $translationsFormsListener, LocaleProviderInterface $localeProvider)
    {
        $this->translationsFormsListener = $translationsFormsListener;
        $this->localeProvider = $localeProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventSubscriber($this->translationsFormsListener);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['default_locale'] = $options['default_locale'];
        $view->vars['required_locales'] = $options['required_locales'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'empty_data' => function (FormInterface $form) {
                return new ArrayCollection();
            },
            'locales' => $this->localeProvider->getLocales(),
            'default_locale' => $this->localeProvider->getDefaultLocale(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            'form_options' => [],
        ]);

        $resolver->setRequired('form_type');

        $resolver->setNormalizer('form_options', function (Options $options, $value): array {
            // Check mandatory data_class option when AutoFormType use
            if (($options['form_type'] instanceof AutoFormType) && !isset($value['data_class'])) {
                throw new \RuntimeException(sprintf('Missing "data_class" option under "form_options" of TranslationsFormsType. Required when "form_type" use "AutoFormType".'));
            }

            return $value;
        });
    }

    public function getBlockPrefix(): string
    {
        return 'a2lix_translationsForms';
    }
}
