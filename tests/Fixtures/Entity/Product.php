<?php

declare(strict_types=1);

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
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity="MediaLocalize", mappedBy="product", indexBy="locale", cascade={"all"}, orphanRemoval=true)
     */
    protected $medias;

    /**
     * @ORM\OneToMany(targetEntity="ProductTranslation", mappedBy="object", indexBy="locale", cascade={"all"}, orphanRemoval=true)
     */
    protected $translations;

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
