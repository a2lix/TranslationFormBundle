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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

#[ORM\Entity]
#[ORM\UniqueConstraint(columns: ['locale', 'object_id', 'field'])]
class ProductTranslation extends AbstractPersonalTranslation
{
    public function __construct(string $locale, string $field, string $value)
    {
        $this->setLocale($locale)->setField($field)->setContent($value);
    }

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'object_id', nullable: false, onDelete: 'CASCADE')]
    protected $object;
}
