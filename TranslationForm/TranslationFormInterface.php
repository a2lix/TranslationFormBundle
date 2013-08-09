<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

/**
 * @author David ALLIX
 */
interface TranslationFormInterface
{
    /**
     *
     */
    public function getChildrenOptions($class, $options);

    /**
     *
     */
    public function guessMissingChildOptions($guesser, $class, $property, $options);
}
