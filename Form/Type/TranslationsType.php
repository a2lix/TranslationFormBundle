<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    A2lix\TranslationFormBundle\Form\EventListener\DefaultTranslationsListener,
    A2lix\TranslationFormBundle\Form\DataMapper\IndexByTranslationMapper;

/**
 * Regroup by locales, all translations fields
 *
 * @author David ALLIX
 */
class TranslationsType extends AbstractType
{
    private $translationsListener;
    private $locales;
    private $requiredLocales;

    /**
     *
     * @param \A2lix\TranslationFormBundle\Form\EventListener\DefaultTranslationsListener $translationsListener
     * @param type $locales
     * @param type $required
     */
    public function __construct(DefaultTranslationsListener $translationsListener, $locales, $requiredLocales)
    {
        $this->translationsListener = $translationsListener;
        $this->locales = $locales;
        $this->requiredLocales = $requiredLocales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setDataMapper(new IndexByTranslationMapper());
        $builder->addEventSubscriber($this->translationsListener);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'by_reference' => false,
            //'required' => true,
            'required_locales' => $this->requiredLocales,
            'locales' => $this->locales,
            'fields' => array(),
        ));
    }

    public function getName()
    {
        return 'a2lix_translations';
    }
}
