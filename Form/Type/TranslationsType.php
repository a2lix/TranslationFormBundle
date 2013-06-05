<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;
use A2lix\TranslationFormBundle\Form\EventListener\DefaultTranslationsSubscriber;
use A2lix\TranslationFormBundle\Form\DataMapper\IndexByTranslationMapper;

/**
 * Regroup by locales, all translations fields
 *
 * @author David ALLIX
 */
class TranslationsType extends AbstractType
{
    private $translationsSubscriber;
    private $locales;
    private $required;

    public function __construct(DefaultTranslationsSubscriber $translationsSubscriber, $locales, $required)
    {
        $this->translationsSubscriber = $translationsSubscriber;
        $this->locales = $locales;
        $this->required = $required;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new IndexByTranslationMapper());

        // Form translation
        if ($options['form']) {
            foreach ($options['locales'] as $locale) {
                $builder->add($locale, $options['form']['type'], $options['form']['options']);
            }

        // Fields translation
        } else {
            $builder->addEventSubscriber($this->translationsSubscriber);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => $this->locales,
            'fields' => array(),
            'form' => array(),
            'by_reference' => false,
        ));
    }

    public function getName()
    {
        return 'a2lix_translations';
    }
}
