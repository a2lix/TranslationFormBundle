<?php

namespace A2lix\TranslationFormBundle\Util\Gedmo;

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

    public function addTranslation($translation)
    {
        if (!$this->translations->contains($translation)) {
            $translation->setObject($this);
            $this->translations->add($translation);
        }
        return $this;
    }

    public function removeTranslation($translation)
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }
        return $this;
    }
}
