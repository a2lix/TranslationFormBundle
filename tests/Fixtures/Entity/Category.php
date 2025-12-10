<?php declare(strict_types=1);

namespace A2lix\TranslationFormBundle\Tests\Fixtures\Entity;

use Doctrine\DBAL\Types\Types;
use A2lix\AutoFormBundle\Form\Attribute\AutoTypeCustom;
use A2lix\TranslationFormBundle\Helper\KnpTranslatableAccessorTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use A2lix\TranslationFormBundle\Tests\Fixtures\IdTrait;

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
