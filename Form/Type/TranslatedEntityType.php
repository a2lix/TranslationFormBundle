<?php

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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Translated entity.
 *
 * @author David ALLIX
 */
class TranslatedEntityType extends AbstractType
{
    private $request;
    private $requestStack;

    // BC for SF 2.3
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // BC for SF < 2.7
        $optionProperty = 'choice_label';
        if (in_array('property', $resolver->getDefinedOptions())) {
            $optionProperty = 'property';
        }

        $resolver->setDefaults([
            'translation_path' => 'translations',
            'translation_property' => null,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('e')
                    ->select('e, t')
                    ->join('e.translations', 't');
            },
            $optionProperty => function (Options $options) {
                return $options['translation_path'].'['.$this->getLocale().'].'.$options['translation_property'];
            },
        ]);
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getParent()
    {
        return
            method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ?
            'Symfony\Bridge\Doctrine\Form\Type\EntityType' :
            'entity';
    }

    // BC for SF < 3.0
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'a2lix_translatedEntity';
    }

    private function getLocale()
    {
        if ($this->requestStack) {
            return $this->requestStack->getCurrentRequest()->getLocale();
        }

        if ($this->request) {
            return $this->request->getLocale();
        }

        throw new \Exception('Error while getting request');
    }
}
