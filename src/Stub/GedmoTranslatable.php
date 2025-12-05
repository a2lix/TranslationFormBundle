<?php

namespace A2lix\TranslationFormBundle\Stub;

use Doctrine\Common\Collections\Collection;

/**
 * @internal
 */
interface GedmoTranslatable
{
    /**
     * @return Collection<int, GedmoTranslation>
     */
    public function getTranslations(): Collection;
    public function addTranslation(GedmoTranslation $translation): self;
    public function removeTranslation(GedmoTranslation $translation): self;
}
