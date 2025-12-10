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
use A2lix\TranslationFormBundle\Tests\Fixtures\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[Gedmo\TranslationEntity(class: ProductTranslation::class)]
class Product
{
    use IdTrait;

    #[ORM\Column]
    #[AutoTypeCustom(options: ['priority' => 1])]
    public string $code;

    #[ORM\Column]
    #[Gedmo\Translatable]
    public ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Translatable]
    public ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['all'])]
    public ?Category $category = null;

    /** @var Collection<int, ProductTranslation> */
    #[ORM\OneToMany(targetEntity: ProductTranslation::class, mappedBy: 'object', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[AutoTypeCustom(options: ['priority' => 1])]
    public Collection $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return Collection<int, ProductTranslation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(ProductTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation->setObject($this);
        }

        return $this;
    }

    public function removeTranslation(ProductTranslation $translation): self
    {
        $this->translations->removeElement($translation->setObject(null));

        return $this;
    }
}
