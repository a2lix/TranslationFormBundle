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
     * @return type
     */
    public function getManagerRegistry()
    {
        return $this->managerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenOptions($class, $options)
    {
        $childrenOptions = array();

        // Clean some options
        unset($options['inherit_data']);
        unset($options['translatable_class']);

        // Custom options by field
        foreach (array_unique(array_merge(array_keys($options['fields']), $this->getTranslatableFields($class))) as $child) {
            $childOptions = (isset($options['fields'][$child]) ? $options['fields'][$child] : array()) + array('required' => $options['required']);

            if (!isset($childOptions['display']) || $childOptions['display']) {
                $childOptions = $this->guessMissingChildOptions($this->typeGuesser, $class, $child, $childOptions);

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
