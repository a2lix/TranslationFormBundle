<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author David ALLIX
 */
class TranslatedEntityType extends AbstractType
{
    /** @var RequestStack */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_path' => 'translations',
            'translation_property' => null,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('e')
                    ->select('e, t')
                    ->join('e.translations', 't');
            },
            'property' => function (Options $options) {
                if (null === ($request = $this->requestStack->getCurrentRequest())) {
                    throw new \RuntimeExceptionn('Error while getting request');
                }

                return $options['translation_path'] . '[' . $request->getLocale() . '].' . $options['translation_property'];
            },
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
