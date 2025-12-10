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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\Entity]
class Company implements TranslatableInterface
{
    use IdTrait;
    use KnpTranslatableAccessorTrait;
    use TranslatableTrait;

    #[ORM\Column]
    #[AutoTypeCustom(options: ['priority' => 2])]
    public string $code;

    #[AutoTypeCustom(options: ['priority' => 1])]
    protected $translations;

    /** @var Collection<int, Category> */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'company', cascade: ['all'], orphanRemoval: true)]
    #[AutoTypeCustom(options: ['entry_options' => ['label' => false]], embedded: true)]
    public Collection $categories;

    /** @var Collection<int, CompanyMediaLocale> */
    #[ORM\OneToMany(targetEntity: CompanyMediaLocale::class, mappedBy: 'company', cascade: ['all'], orphanRemoval: true, indexBy: 'locale')]
    // #[AutoTypeCustom(embedded: true, options: ['entry_options' => ['label' => false, 'children_excluded' => ['id']]])]
    // #[AutoTypeCustom(options: ['form_type' => CompanyMediaType::class], type: TranslationsFormsType::class)]
    public Collection $medias;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->medias = new ArrayCollection();
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $category->company = $this;
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function addMedia(CompanyMediaLocale $media): self
    {
        $media->company = $this;
        $this->medias->set($media->locale, $media);

        return $this;
    }

    public function removeMedia(CompanyMediaLocale $media): self
    {
        $this->medias->removeElement($media);

        return $this;
    }

    public function getMedia(?string $targetedLocale = null): ?CompanyMediaLocale
    {
        $targetedLocale ??= $this->getCurrentLocale();

        return $this->medias->get($targetedLocale) ?? null;
    }
}
