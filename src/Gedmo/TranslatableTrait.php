<?php

declare(strict_types=1);

namespace A2lix\TranslationFormBundle\Gedmo;

trait TranslatableTrait
{
    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation($translation)
    {
        if (!$this->translations->contains($translation)) {
            $translation->setObject($this);
            $this->translations[] = $translation;
        }
    }
}
