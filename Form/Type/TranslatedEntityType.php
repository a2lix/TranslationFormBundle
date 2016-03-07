<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Doctrine\ORM\EntityRepository,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\OptionsResolver\Options,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Translated entity
 *
 * @author David ALLIX
 */
class TranslatedEntityType extends AbstractType
{
    private $requestStack;
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_path' => 'translations',
            'translation_property' => null,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('e')
                    ->select('e, t')
                    ->join('e.translations', 't');
            },
            'choice_label' => function(Options $options) {
                if (null === $this->requestStack->getCurrentRequest() and null === $this->request) {
                    throw new \Exception('Error while getting request');
                } 
                
                if(null !== $this->requestStack->getCurrentRequest()) {
                    return $options['translation_path'] .'['. $this->requestStack->getCurrentRequest()->getLocale() .'].'. $options['translation_property'];
                } else {
                    return $options['translation_path'] .'['. $this->request->getLocale() .'].'. $options['translation_property'];
                }
            },
        ));

    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getParent()
    {
        return EntityType::class;
    }

    // BC for SF < 3.0
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return TranslatedEntityType::class;
    }
}
