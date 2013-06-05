<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Symfony\Component\Form\FormRegistry,
    Doctrine\Common\Persistence\ObjectManager;

/**
 * @author David ALLIX
 */
abstract class TranslationForm implements TranslationFormInterface
{
    private $translatableClass;
    private $translationClass;
    private $translatableFields;

    private $typeGuesser;
    private $om;

    public function __construct(FormRegistry $formRegistry, ObjectManager $om)
    {
        $this->typeGuesser = $formRegistry->getTypeGuesser();
        $this->om = $om;
    }

    public function getObjectManager()
    {
        return $this->om;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatableClass()
    {
        return $this->translatableClass;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatableClass($translatableClass)
    {
        $this->translatableClass = $translatableClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationClass()
    {
        return $this->translationClass;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslationClass($translationClass)
    {
        $this->translationClass = $translationClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatableFields()
    {
        return $this->translatableFields;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslatableFields($translatableFields)
    {
        $this->translatableFields = $translatableFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return "en";
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenOptions($options)
    {
        $childrenOptions = array();
        $translatableClass = $this->getTranslatableClass();

        // Custom options by field
        foreach ($this->getTranslatableFields() as $child) {
            $childOptions = (isset($options['fields'][$child]) ? $options['fields'][$child] : array()) + array('required' => $options['required']);

            if (!isset($childOptions['display']) || $childOptions['display']) {
                $childOptions = $this->guessMissingChildOptions($this->typeGuesser, $translatableClass, $child, $childOptions);

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

                // General options for all locales
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
     * {@inheritdoc}
     */
    public function guessMissingChildOptions($guesser, $class, $property, $options)
    {
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
