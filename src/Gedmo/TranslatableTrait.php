<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Gedmo;

trait TranslatableTrait
{
    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation($translation): void
    {
        if (!$this->translations->contains($translation)) {
            $translation->setObject($this);
            $this->translations[] = $translation;
        }
    }
}
