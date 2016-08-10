<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Fixtures\Entity;

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
        $this->medias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(ProductTranslation $translation)
    {
        if (!$this->translations->contains($translation)) {
            $translation->setTranslatable($this);
            $this->translations->set($translation->getLocale(), $translation);
        }

        return $this;
    }

    public function removeTranslation(ProductTranslation $translation)
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }

        return $this;
    }

    public function getMedias()
    {
        return $this->medias;
    }

    public function addMedia(MediaLocalize $media)
    {
        if (!$this->medias->contains($media)) {
            $media->setProduct($this);
            $this->medias->set($media->getLocale(), $media);
        }

        return $this;
    }

    public function removeMedia(Media $media)
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }
}
