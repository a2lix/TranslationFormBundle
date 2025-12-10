<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Fixtures\Entity;

use A2lix\AutoFormBundle\Form\Attribute\AutoTypeCustom;
use A2lix\TranslationFormBundle\Helper\KnpTranslatableAccessorTrait;
use A2lix\TranslationFormBundle\Tests\Fixtures\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\Entity]
class Category implements \Stringable, TranslatableInterface
{
    use IdTrait;
    use KnpTranslatableAccessorTrait;
    use TranslatableTrait;

    #[ORM\Column]
    public string $code;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'categories')]
    #[AutoTypeCustom(excluded: true)]
    public ?Company $company = null;

    public function __toString(): string
    {
        return $this->code;
    }
}
