<?php

declare(strict_types=1);

namespace A2lix\TranslationFormBundle\Gedmo;

trait TranslationTrait
{
    public function __construct(string $locale, string $field, string $value)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
    }
}
