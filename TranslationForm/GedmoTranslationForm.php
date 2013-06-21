<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Gedmo\Translatable\TranslatableListener;

/**
 * @author David ALLIX
 */
class GedmoTranslationForm extends TranslationForm
{
    private $gedmoTranslatableListener;

    /**
     *
     * @return type
     */
    public function getGedmoTranslatableListener()
    {
        return $this->gedmoTranslatableListener;
    }

    /**
     *
     * @param \Gedmo\Translatable\TranslatableListener $gedmoTranslatableListener
     */
    public function setGedmoTranslatableListener(TranslatableListener $gedmoTranslatableListener)
    {
        $this->gedmoTranslatableListener = $gedmoTranslatableListener;
    }

    /**
     * {@inheritdoc}
     */
    public function init($translatableClass)
    {
        $translatableClass = \Doctrine\Common\Util\ClassUtils::getRealClass($translatableClass);
        $manager = $this->getManagerRegistry()->getManagerForClass($translatableClass);
        $gedmoTranslatableListenerConfig = $this->gedmoTranslatableListener->getConfiguration($manager, $translatableClass);

        $this->setTranslatableClass($gedmoTranslatableListenerConfig['useObjectClass']);
        $this->setTranslationClass($gedmoTranslatableListenerConfig['translationClass']);
        $this->setTranslatableFields(isset($gedmoTranslatableListenerConfig['fields']) ? $gedmoTranslatableListenerConfig['fields'] : array());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->gedmoTranslatableListener->getDefaultLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        return $this->gedmoTranslatableListener->getListenerLocale();
    }

    /**
     *
     * @return booldean
     */
    public function getPersistDefaultLocaleTranslation()
    {
        return $this->gedmoTranslatableListener->getPersistDefaultLocaleTranslation();
    }

    /**
     *
     * @param array $locales
     * @return array
     */
    public function getSortedLocales($locales)
    {
        $defaultLocale = $this->getDefaultLocale();
        $isPersistDefaultLocaleTranslation = $this->getPersistDefaultLocaleTranslation();

        $distinctLocales = array();
        foreach ($locales as $locale) {
            if ($isPersistDefaultLocaleTranslation || ($defaultLocale !== $locale)) {
                $distinctLocales['translationsLocales'][] = $locale;
            } else {
                $distinctLocales['defaultLocale'] = $locale;
            }
        }

        return $distinctLocales;
    }
}
