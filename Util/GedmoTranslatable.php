<?php

namespace A2lix\TranslationFormBundle\Util;

/**
 * Translatable trait.
 *
 * Should be used inside entity, that needs to be translated.
 */
trait GedmoTranslatable
{
    public function getTranslations()
    {
        return $this->translations;
    }

    public function setTranslations(\Doctrine\Common\Collections\ArrayCollection $translations)
    {
        $this->translations = $translations;
        return $this;
    }

    public function addTranslation($translation)
    {
        $translation->setObject($this);
        $this->translations[] = $translation;
        return $this;
    }

    public function removeTranslation($translation)
    {
        $this->translations->removeElement($translation);
    }
}
