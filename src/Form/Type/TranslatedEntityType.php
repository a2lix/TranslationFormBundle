<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatedEntityType extends AbstractType
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {}

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_path' => 'translations',
            'query_builder' => static fn (EntityRepository $er) => $er->createQueryBuilder('e')
                ->select('e, t')
                ->join('e.translations', 't'),
            'choice_label' => function (Options $options) {
                if (null === ($request = $this->requestStack->getCurrentRequest())) {
                    throw new \RuntimeException('Error while getting request');
                }

                return \sprintf(
                    '%s[%s].%s',
                    $options['translation_path'],
                    $request->getLocale(),
                    $options['translation_property'],
                );
            },
        ]);

        $resolver->setRequired('translation_property');
        $resolver->setAllowedTypes('translation_property', 'string');
    }

    #[\Override]
    public function getParent(): string
    {
        return EntityType::class;
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'a2lix_translatedEntity';
    }
}
