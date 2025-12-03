<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Helper;

use Doctrine\ORM\Mapping as ORM;

trait OneLocaleTrait
{
    #[ORM\Column(length: 10)]
    public string $locale;

    abstract public function isEmpty(): bool;
}
