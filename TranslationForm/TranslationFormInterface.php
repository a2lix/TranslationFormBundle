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
    public function init($translatableClass);

    /**
     *
     */
    public function getTranslatableClass();

    /**
     *
     */
    public function setTranslatableClass($translatableClass);

    /**
     *
     */
    public function getTranslationClass();

    /**
     *
     */
    public function setTranslationClass($translationClass);

    /**
     *
     */
    public function getTranslatableFields();

    /**
     *
     */
    public function setTranslatableFields($translatableFields);

    /**
     *
     */
    public function getDefaultLocale();

    /**
     *
     */
    public function getCurrentLocale();
}
