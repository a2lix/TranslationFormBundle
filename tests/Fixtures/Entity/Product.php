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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?string $url = null;

    #[ORM\OneToMany(targetEntity: MediaLocalize::class, mappedBy: 'product', cascade: ['all'], orphanRemoval: true, indexBy: 'locale')]
    private ArrayCollection $medias;

    #[ORM\OneToMany(targetEntity: ProductTranslation::class, mappedBy: 'object', cascade: ['all'], orphanRemoval: true, indexBy: 'locale')]
    private ArrayCollection $translations;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
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
            $translation->setTranslatable($this);
            $this->translations->set($translation->getLocale(), $translation);
        }

        return $this;
    }

    public function removeTranslation(ProductTranslation $translation): self
    {
        $this->translations->removeElement($translation);

        return $this;
    }

    /**
     * @return Collection<int, MediaLocalize>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(MediaLocalize $media): self
    {
        if (!$this->medias->contains($media)) {
            $media->setProduct($this);
            $this->medias->set($media->getLocale(), $media);
        }

        return $this;
    }

    public function removeMedia(MediaLocalize $media): self
    {
        $this->medias->removeElement($media);

        return $this;
    }
}
