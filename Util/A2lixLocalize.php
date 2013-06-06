<?php

namespace A2lix\TranslationFormBundle\Util;

/**
 * Localize trait.
 *
 * Should be used inside entity, that needs to be localized
 */
trait A2lixLocalize
{
    /**
     * @ORM\Column(length=10)
     */
    protected $locale;

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }
}
