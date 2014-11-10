<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Symfony\Component\Form\FormRegistry,
    Doctrine\Common\Persistence\ManagerRegistry,
    Doctrine\Common\Util\ClassUtils;

/**
 * @author David ALLIX
 */
class TranslationForm implements TranslationFormInterface
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
     * @param string $translationClass
     * @param array  $exclude
     * @return array
     */
    protected function getTranslationFields($translationClass, array $exclude = array())
    {
        $fields = array();
        $translationClass = ClassUtils::getRealClass($translationClass);

        if ($manager = $this->managerRegistry->getManagerForClass($translationClass)) {
            $metadataClass = $manager->getMetadataFactory()->getMetadataFor($translationClass);

            foreach ($metadataClass->fieldMappings as $fieldMapping) {
                if (!in_array($fieldMapping['fieldName'], array('id', 'locale')) && !in_array($fieldMapping['fieldName'], $exclude)) {
                    $fields[] = $fieldMapping['fieldName'];
                }
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsOptions($class, $options)
    {
        $fieldsOptions = array();

        foreach ($this->getFieldsList($options, $class) as $field) {
            $fieldOptions = isset($options['fields'][$field]) ? $options['fields'][$field] : array();

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
     * Combine formFields with translationFields. (Useful for upload field)
     */
    private function getFieldsList($options, $class)
    {
        $formFields = array_keys($options['fields']);

        // Check existing
        foreach ($formFields as $field) {
            if (!property_exists($class, $field)) {
                throw new \Exception("Field '". $field ."' doesn't exist in ". $class);
            }
        }

        return array_unique(array_merge($formFields, $this->getTranslationFields($class, $options['exclude_fields'])));
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
