<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;
use A2lix\TranslationFormBundle\TranslationForm\TranslationForm,
    A2lix\TranslationFormBundle\Form\DataMapper\TranslationMapper;

/**
 * Regroup by locales, all translations fields
 *
 * @author David ALLIX
 */
class TranslationsType extends AbstractType
{
    private $translationForm;
    private $locales;
    private $required;

    public function __construct(TranslationForm $translationForm, $locales, $required)
    {
        $this->translationForm = $translationForm;
        $this->locales = $locales;
        $this->required = $required;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translatableConfig = $this->translationForm->initTranslatableConfiguration($builder->getParent()->getDataClass());
        $childrenOptions = $this->translationForm->getChildrenOptions($options);

        $builder->setDataMapper(new TranslationMapper($translatableConfig['translationClass']));

        foreach ($options['locales'] as $locale) {
            if (isset($childrenOptions[$locale])) {
                $builder->add($locale, 'a2lix_translationsLocale', array(
                    'fields' => $childrenOptions[$locale]
                ));
            }
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_locale'] = (array) $this->translationForm->getDefaultLocale();
        $view->vars['locales'] = $options['locales'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => $this->locales,
            'required' => $this->required,
            'fields' => array()
        ));
    }

    public function getName()
    {
        return 'a2lix_translations';
    }
}
