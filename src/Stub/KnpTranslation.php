<?php

namespace A2lix\TranslationFormBundle\Stub;

/**
 * @internal
 */
interface KnpTranslation
{
    public function setLocale(string $locale): void;
    public function isEmpty(): bool;
}
