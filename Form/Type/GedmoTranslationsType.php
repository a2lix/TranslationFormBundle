<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    A2lix\TranslationFormBundle\TranslationForm\GedmoTranslationForm;

/**
 * Regroup by locales, all translations fields
 *
 * @author David ALLIX
 */
class GedmoTranslationsType extends AbstractType
{
    private $translationForm;
    private $locales;
    private $required;

    /**
     *
     * @param \A2lix\TranslationFormBundle\TranslationForm\GedmoTranslationForm $translationForm
     * @param type $locales
     * @param type $required
     */
    public function __construct(GedmoTranslationForm $translationForm, $locales, $required)
    {
        $this->translationForm = $translationForm;
        $this->locales = $locales;
        $this->required = $required;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->translationForm->init($options['translatable_class']);
        $childrenOptions = $this->translationForm->getChildrenOptions($options);
        $locales = $this->translationForm->getSortedLocales($options['locales']);

        if (isset($locales['defaultLocale'])) {
            $builder->add('defaultLocale', 'a2lix_translationsLocales_gedmo', array(
                'locales' => (array) $locales['defaultLocale'],
                'fields_options' => $childrenOptions,
                'inherit_data' => true,
            ));
        }

        $builder->add($builder->getName(), 'a2lix_translationsLocales_gedmo', array(
            'locales' => $locales['translationsLocales'],
            'fields_options' => $childrenOptions,
            'translation_class' => $this->translationForm->getTranslationClass()
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required' => $this->required,
            'locales' => $this->locales,
            'fields' => array(),
            'form' => array(),
            'translatable_class' => null,
            'inherit_data' => true,
        ));
    }

    public function getName()
    {
        return 'a2lix_translations_gedmo';
    }
}
