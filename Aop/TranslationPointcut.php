<?php

namespace A2lix\TranslationFormBundle\Aop;

use Doctrine\Common\Annotations\Reader,
    JMS\AopBundle\Aop\PointcutInterface;

/**
 * @author David ALLIX
 */
class TranslationPointcut implements PointcutInterface
{
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function matchesClass(\ReflectionClass $class)
    {
        return (substr($class->name, -10) === 'Controller');
    }

    public function matchesMethod(\ReflectionMethod $method)
    {
        // Sonata
        if (('Sonata\AdminBundle\Controller\CRUDController' === $method->class) &&
            in_array($method->name, array('createAction', 'editAction'))) {

            return true;
        }

        return (null !== $this->reader->getMethodAnnotation($method, 'A2lix\TranslationFormBundle\Annotation\A2lixTranslation'));
    }
}