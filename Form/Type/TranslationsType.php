<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormRegistry;
use Doctrine\ORM\EntityManager;
use Gedmo\Translatable\TranslatableListener;
use A2lix\TranslationFormBundle\EventListener\TranslationFormSubscriber;

/**
 * Regroup by locales, all translations fields
 *
 * @author David ALLIX
 */
class TranslationsType extends AbstractType
{
    private $formRegistry;
    private $em;
    private $translatableListener;
    private $defaultLocale;
    private $locales;
    private $required;

    public function __construct(FormRegistry $formRegistry, EntityManager $em, TranslatableListener $translatableListener, $defaultLocale, $locales, $required)
    {
        $this->formRegistry = $formRegistry;
        $this->em = $em;
        $this->translatableListener = $translatableListener;
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
        $this->required = $required;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translatableConfig = $this->translatableListener->getConfiguration($this->em, $builder->getParent()->getDataClass());
        $childrenOptions = $this->getChildrenOptions($translatableConfig['useObjectClass'], $translatableConfig['fields'], $options);

        foreach ($options['locales'] as $locale) {
            if (isset($childrenOptions[$locale])) {
                $builder->add($locale, 'a2lix_translationsLocale', array(
                    'fields' => $childrenOptions[$locale]
                ));
            }
        }

        $subscriber = new TranslationFormSubscriber($builder->getFormFactory(), $translatableConfig['translationClass']);
        $builder->addEventSubscriber($subscriber);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->set('default_locale', (array) $this->defaultLocale);
        $view->set('locales', $options['locales']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'by_reference' => false,
            'locales' => $this->locales,
            'required' => $this->required,
            'fields' => array()
        ));
    }

    public function getName()
    {
        return 'a2lix_translations';
    }


    /**
     *
     * @param type $entityClass
     * @param type $translatableFields
     * @param type $options
     * @return type
     */
    private function getChildrenOptions($entityClass, $translatableFields, $options)
    {
        $childrenOptions = array();
        $guesser = $this->formRegistry->getTypeGuesser();

        foreach ($translatableFields as $child) {
            $childOptions = isset($options['fields'][$child]) ? $options['fields'][$child] : array();

            if (!isset($childOptions['display']) || $childOptions['display']) {
                $childOptions = $this->guessMissingChildOptions($guesser, $entityClass, $child, $childOptions);

                // Custom options by locale
                if (isset($childOptions['locale_options'])) {
                    $localesChildOptions = $childOptions['locale_options'];
                    unset($childOptions['locale_options']);

                    foreach ($options['locales'] as $locale) {
                        $localeChildOptions = isset($localesChildOptions[$locale]) ? $localesChildOptions[$locale] : array();
                        if (!isset($localeChildOptions['display']) || $localeChildOptions['display']) {
                            $childrenOptions[$locale][$child] = $localeChildOptions + $childOptions;
                        }
                    }

                } else {
                    foreach ($options['locales'] as $locale) {
                        $childrenOptions[$locale][$child] = $childOptions;
                    }
                }
            }
        }

        return $childrenOptions;
    }

    /**
     *
     * @param type $guesser
     * @param type $class
     * @param type $property
     * @param type $options
     * @return type
     */
    private function guessMissingChildOptions($guesser, $class, $property, $options)
    {
        if (!isset($options['label'])) {
            $options['label'] = ucfirst($property);
        }

        if (!isset($options['required'])) {
            $options['required'] = $this->required;
        }

        if (!isset($options['type']) && ($typeGuess = $guesser->guessType($class, $property))) {
            $options['type'] = $typeGuess->getType();
        }

        if (!isset($options['pattern']) && ($patternGuess = $guesser->guessPattern($class, $property))) {
            $options['pattern'] = $patternGuess->getValue();
        }

        if (!isset($options['max_length']) && ($maxLengthGuess = $guesser->guessMaxLength($class, $property))) {
            $options['max_length'] = $maxLengthGuess->getValue();
        }

        return $options;
    }
}