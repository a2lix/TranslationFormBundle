<?php

namespace A2lix\TranslationFormBundle\Stub;

/**
 * @internal
 */
interface GedmoTranslation
{
    public function getLocale(): string;
    public function setLocale(string $locale): void;
    public function getField(): string;
    public function getContent(): ?string;
    public function setContent(?string $content): void;
    public function isEmpty(): bool;
}
