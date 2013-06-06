<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    A2lix\TranslationFormBundle\Form\EventListener\DefaultTranslationsSubscriber,
    A2lix\TranslationFormBundle\Form\DataMapper\IndexByTranslationMapper;

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

    /**
     *
     * @param \A2lix\TranslationFormBundle\Form\EventListener\DefaultTranslationsSubscriber $translationsSubscriber
     * @param type $locales
     * @param type $required
     */
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
        if ($options['form'] && isset($options['form']['type'])) {
            $formType = $options['form']['type'];
            $formOptions = isset($options['form']['options']) ? $options['form']['options'] : array();
            foreach ($options['locales'] as $locale) {
                $builder->add($locale, $formType, $formOptions);
            }

        // Fields translation
        } else {
            $builder->addEventSubscriber($this->translationsSubscriber);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required' => $this->required,
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
