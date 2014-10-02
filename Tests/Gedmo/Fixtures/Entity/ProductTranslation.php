<?php

namespace A2lix\TranslationFormBundle\Tests\Gedmo\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="Product_translations", uniqueConstraints={
 *    @ORM\UniqueConstraint(name="lookup_unique_idx", columns={"locale", "object_id"})
 * })
 */
class ProductTranslation extends AbstractTranslation
{
   /**
    * @ORM\ManyToOne(targetEntity="Product", inversedBy="translations")
    * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
    */
    protected $object;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

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
}
