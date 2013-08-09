<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Gedmo\Translatable\TranslatableListener;

/**
 * @author David ALLIX
 */
class GedmoTranslationForm extends TranslationForm
{
    private $gedmoTranslatableListener;
    private $gedmoConfig;

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
     *
     * @param type $translatableClass
     * @return type
     */
    private function getGedmoConfig($translatableClass)
    {
        if (isset($this->gedmoConfig[$translatableClass])) {
            return $this->gedmoConfig[$translatableClass];
        }

        $translatableClass = \Doctrine\Common\Util\ClassUtils::getRealClass($translatableClass);
        $manager = $this->getManagerRegistry()->getManagerForClass($translatableClass);
        $this->gedmoConfig[$translatableClass] = $this->gedmoTranslatableListener->getConfiguration($manager, $translatableClass);

        return $this->gedmoConfig[$translatableClass];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationClass($translatableClass)
    {
        $gedmoConfig = $this->getGedmoConfig($translatableClass);
        return $gedmoConfig['translationClass'];
    }

    /**
     *
     * @param type $translatableClass
     * @return type
     */
    protected function getTranslatableFields($translatableClass)
    {
        $gedmoConfig = $this->getGedmoConfig($translatableClass);
        return isset($gedmoConfig['fields']) ? $gedmoConfig['fields'] : array();
    }
}
