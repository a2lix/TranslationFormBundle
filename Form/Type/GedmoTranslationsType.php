<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    A2lix\TranslationFormBundle\Form\EventListener\GedmoTranslationsListener,
    A2lix\TranslationFormBundle\TranslationForm\GedmoTranslationForm,
    A2lix\TranslationFormBundle\Form\DataMapper\GedmoTranslationMapper,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\OptionsResolver\Options;

/**
 * Regroup by locales, all translations fields (gedmo)
 *
 * @author David ALLIX
 */
class GedmoTranslationsType extends AbstractType
{
    private $translationsListener;
    private $translationForm;
    private $locales;
    //private $required;
    private $requiredLocales;

    /**
     *
     * @param \A2lix\TranslationFormBundle\Form\EventListener\GedmoTranslationsListener $translationsListener
     * @param \A2lix\TranslationFormBundle\TranslationForm\GedmoTranslationForm $translationForm
     * @param type $locales
     * @param type $required
     */
    public function __construct(GedmoTranslationsListener $translationsListener, GedmoTranslationForm $translationForm, $locales, array $requiredLocales)
    {
        $this->translationsListener = $translationsListener;
        $this->translationForm = $translationForm;
        $this->locales = $locales;
        $this->requiredLocales = $requiredLocales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Simple way is enough
        if (!$options['inherit_data']) {
            $builder->setDataMapper(new GedmoTranslationMapper());
            $builder->addEventSubscriber($this->translationsListener);

        } else {
            if (!$options['translatable_class']) {
                throw new \Exception("If you want include the default locale with translations locales, you need to fill the 'translatable_class' option");
            }

            $childrenOptions = $this->translationForm->getChildrenOptions($options['translatable_class'], $options);
            $defaultLocale = (array) $this->translationForm->getGedmoTranslatableListener()->getDefaultLocale();

            $builder->add('defaultLocale', 'a2lix_translationsLocales_gedmo', array(
                'locales' => $defaultLocale,
                'fields_options' => $childrenOptions,
                'inherit_data' => true,
                'required_locales' => $this->requiredLocales
            ));

            $builder->add($builder->getName(), 'a2lix_translationsLocales_gedmo', array(
                'locales' => array_diff($options['locales'], $defaultLocale),
                'fields_options' => $childrenOptions,
                'inherit_data' => false,
                'translation_class' => $this->translationForm->getTranslationClass($options['translatable_class']),
                'required_locales' => $this->requiredLocales
            ));
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['simple_way'] = !$options['inherit_data'];
        $view->vars['required_locales'] = $options['required_locales'];
    }

    /**
     * BC for SF < 2.7
     * 
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $translatableListener = $this->translationForm->getGedmoTranslatableListener();

        $resolver->setDefaults(array(
            //'required' => false,
            'required_locales' => $this->requiredLocales,
            'locales' => $this->locales,
            'fields' => array(),
            'translatable_class' => null,

            // inherit_data is needed only if there is no persist of default locale and default locale is required to display
            'inherit_data' => function(Options $options) use ($translatableListener) {
                return (!$translatableListener->getPersistDefaultLocaleTranslation()
                    && (in_array($translatableListener->getDefaultLocale(), $options['locales'])));
            },
        ));
    }

    /**
     * BC for SF < 2.8
     *
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'a2lix_translations_gedmo';
    }
}
