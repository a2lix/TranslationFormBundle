<?php

namespace A2lix\TranslationFormBundle\Tests\Gedmo\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @Gedmo\Translatable
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Translatable
     */
    protected $description;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $url;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $medias
     * @ORM\OneToMany(targetEntity="Media", mappedBy="product", indexBy="locale", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $medias;

    /**
     * @ORM\OneToMany(targetEntity="ProductTranslation", mappedBy="object", indexBy="locale", cascade={"all"}, orphanRemoval=true)
     * @Assert\Valid
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
            $translation->setObject($this);
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

    public function addMedia(Media $media)
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
