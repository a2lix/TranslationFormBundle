<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Symfony\Component\Form\FormRegistry,
    Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author David ALLIX
 */
abstract class TranslationForm implements TranslationFormInterface
{
    private $typeGuesser;
    private $managerRegistry;

    /**
     *
     * @param \Symfony\Component\Form\FormRegistry $formRegistry
     * @param \Doctrine\Common\Persistence\ManagerRegistry $managerRegistry
     */
    public function __construct(FormRegistry $formRegistry, ManagerRegistry $managerRegistry)
    {
        $this->typeGuesser = $formRegistry->getTypeGuesser();
        $this->managerRegistry = $managerRegistry;
    }

    /**
     *
     * @return \Doctrine\Common\Persistence\ManagerRegistry
     */
    public function getManagerRegistry()
    {
        return $this->managerRegistry;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFieldsOptions($class, $options)
    {
        $fieldsOptions = array();

        // Add additionnal fields if necessary (Useful for upload field)
        $extendedFields = $this->getTranslatableFields($class) + array_keys($options['fields']);
        
        foreach ($extendedFields as $field) {
            $fieldOptions = (isset($options['fields'][$field]) ? $options['fields'][$field] : array()) + array('required' => $options['required']);

            if (!isset($fieldOptions['display']) || $fieldOptions['display']) {
                $fieldOptions = $this->guessMissingFieldOptions($this->typeGuesser, $class, $field, $fieldOptions);

                // Custom options by locale
                if (isset($fieldOptions['locale_options'])) {
                    $localesFieldOptions = $fieldOptions['locale_options'];
                    unset($fieldOptions['locale_options']);

                    foreach ($options['locales'] as $locale) {
                        $localeFieldOptions = isset($localesFieldOptions[$locale]) ? $localesFieldOptions[$locale] : array();
                        if (!isset($localeFieldOptions['display']) || $localeFieldOptions['display']) {
                            $fieldsOptions[$locale][$field] = $localeFieldOptions + $fieldOptions;
                        }
                    }

                // General options for all locales
                } else {
                    foreach ($options['locales'] as $locale) {
                        $fieldsOptions[$locale][$field] = $fieldOptions;
                    }
                }
            }
        }

        return $fieldsOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormsOptions($options)
    {
        $formsOptions = array();
        
        // Current options
        $formOptions = $options['form_options'];
        
        // Custom options by locale
        if (isset($formOptions['locale_options'])) {
            $localesFormOptions = $formOptions['locale_options'];
            unset($formOptions['locale_options']);
            
            foreach ($options['locales'] as $locale) {
                $localeFormOptions = isset($localesFormOptions[$locale]) ? $localesFormOptions[$locale] : array();
                if (!isset($localeFormOptions['display']) || $localeFormOptions['display']) {
                    $formsOptions[$locale] = $localeFormOptions + $formOptions;
                }
            }
            
        // General options for all locales
        } else {
            foreach ($options['locales'] as $locale) {
                $formsOptions[$locale] = $formOptions;
            }
        }

        return $formsOptions;
    }
    
    /**
     * {@inheritdoc}
     */
    public function guessMissingFieldOptions($guesser, $class, $property, $options)
    {
        if (!isset($options['field_type']) && ($typeGuess = $guesser->guessType($class, $property))) {
            $options['field_type'] = $typeGuess->getType();
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
