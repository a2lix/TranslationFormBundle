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
use A2lix\TranslationFormBundle\Helper\OneLocaleInterface;
use A2lix\TranslationFormBundle\Helper\OneLocaleTrait;
use A2lix\TranslationFormBundle\Tests\Fixtures\IdTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CompanyMediaLocale implements \Stringable, OneLocaleInterface
{
    use IdTrait;
    use OneLocaleTrait;

    #[ORM\Column]
    public ?string $url;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'medias')]
    #[AutoTypeCustom(excluded: true)]
    public ?Company $company = null;

    public function isEmpty(): bool
    {
        return null === $this->url;
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s',
            $this->url,
        );
    }
}
