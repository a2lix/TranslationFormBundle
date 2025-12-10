<?php declare(strict_types=1);

namespace A2lix\TranslationFormBundle\Tests\Fixtures\Entity;

use A2lix\TranslationFormBundle\Tests\Fixtures\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

#[ORM\Entity]
class CompanyTranslation implements TranslationInterface
{
    use IdTrait;
    use TranslationTrait;

    #[ORM\Column]
    public ?string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;
}
