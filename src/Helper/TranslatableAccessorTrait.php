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

use Symfony\Component\PropertyAccess\PropertyAccess;

trait TranslatableAccessorTrait
{
    public function __call($method, $arguments)
    {
        return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $method);
    }

    public function __get($property)
    {
        return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $property);
    }
}
