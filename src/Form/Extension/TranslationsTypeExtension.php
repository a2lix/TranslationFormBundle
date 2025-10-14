<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Extension;

use A2lix\AutoFormBundle\Form\Type\AutoType;
use Symfony\Component\Form\AbstractTypeExtension;

class TranslationsTypeExtension extends AbstractTypeExtension
{
    #[\Override]
    public static function getExtendedTypes(): iterable
    {
        return [AutoType::class];
    }

    // TODO
}
