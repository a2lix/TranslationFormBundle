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
            'property' => function (Options $options) {
                if (null === $this->request) {
                    throw new \Exception('Error while getting request');
                }

                return $options['translation_path'].'['.$this->request->getLocale().'].'.$options['translation_property'];
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
        return 'entity';
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
}
