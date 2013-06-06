<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Symfony\Component\Form\FormRegistry,
    Doctrine\Common\Persistence\ObjectManager,
    Gedmo\Translatable\TranslatableListener;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * @author David ALLIX
 */
class TranslationForm
{
    private $guesser;
    private $om;
    private $translatableListener;
    private $translatableConfig = array();
    private $doctrine;

    public function __construct(FormRegistry $formRegistry, ObjectManager $om, TranslatableListener $translatableListener, Registry $doctrine)
    {
        $this->guesser = $formRegistry->getTypeGuesser();
        $this->om = $om;
        $this->translatableListener = $translatableListener;
        $this->doctrine = $doctrine;
    }

    public function initTranslatableConfiguration($class, $entity_manager)
    {
      if($entity_manager == null){
        return $this->translatableConfig = $this->translatableListener->getConfiguration($this->om, $class);
      }else{
        return $this->translatableConfig = $this->translatableListener->getConfiguration($this->doctrine->getManager($entity_manager), $class);
      }
    }

    public function getDistinctLocales($locales)
    {
        $defaultLocale = $this->translatableListener->getDefaultLocale();

        $distinctLocales = array();
        foreach ($locales as $locale) {
            if ($defaultLocale !== $locale) {
                $distinctLocales['translations'][] = $locale;
            } else {
                $distinctLocales['default'] = $locale;
            }
        }

        return $distinctLocales;
    }

    public function getListenerLocale()
    {
        return $this->translatableListener->getListenerLocale();
    }

    public function getDefaultLocale()
    {
        return $this->translatableListener->getDefaultLocale();
    }


    /**
     *
     * @param array $options Initial options
     *
     * @return options Options of all fields
     */
    public function getChildrenOptions($options)
    {
        $childrenOptions = array();

        // Custom options by field
        foreach ($this->translatableConfig['fields'] as $child) {
            $childOptions = (isset($options['fields'][$child]) ? $options['fields'][$child] : array()) + array('required' => $options['required']);

            if (!isset($childOptions['display']) || $childOptions['display']) {
                $childOptions = $this->guessMissingChildOptions($this->guesser, $this->translatableConfig['useObjectClass'], $child, $childOptions);

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
     * Use guesser for fill missing options of a field
     *
     * @param object $guesser
     * @param object $class
     * @param string $property
     * @param array $options
     *
     * @return array $options Options of field
     */
    private function guessMissingChildOptions($guesser, $class, $property, $options)
    {
        if (!isset($options['label'])) {
            $options['label'] = ucfirst($property);
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
