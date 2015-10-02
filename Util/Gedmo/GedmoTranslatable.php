<?php

/*
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function addTranslation(\Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $translation->setObject($this);
            $this->translations->set($translation->getLocale(), $translation);
        }

        return $this;
    }

    public function removeTranslation(\Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation $translation)
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }

        return $this;
    }
}
