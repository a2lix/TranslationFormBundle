<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase as BaseTypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class TypeTestCase extends BaseTypeTestCase
{
    /** @var DefaultManipulator */
    protected $defaultFormManipulator;

    protected function setUp()
    {
        parent::setUp();

        $validator = $this->getMock('\Symfony\Component\Validator\Validator\ValidatorInterface');
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(
                new FormTypeValidatorExtension($validator)
            )
            ->addTypeGuesser(
                $this->getMockBuilder('Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser')
                     ->disableOriginalConstructor()
                     ->getMock()
            )
            ->getFormFactory();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function getDefaultFormManipulator()
    {
        if (null !== $this->defaultFormManipulator) {
            return $this->defaultFormManipulator;
        }

        $config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/../Fixtures/Entity'], true, null, null, false);
        $entityManager = EntityManager::create(['driver' => 'pdo_sqlite'], $config);
        $doctrineInfo = new \A2lix\AutoFormBundle\ObjectInfo\DoctrineInfo($entityManager->getMetadataFactory());

        return $this->defaultFormManipulator = new \A2lix\AutoFormBundle\Form\Manipulator\DefaultManipulator($doctrineInfo, ['id', 'locale', 'translatable']);
    }

    protected function getConfiguredAutoFormType()
    {
        $AutoFormListener = new \A2lix\AutoFormBundle\Form\EventListener\AutoFormListener($this->getDefaultFormManipulator());

        return new \A2lix\AutoFormBundle\Form\Type\AutoFormType($AutoFormListener);
    }

    protected function getConfiguredTranslationsType($locales, $defaultLocale, $requiredLocales)
    {
        $translationsListener = new \A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener($this->getDefaultFormManipulator());
        $localProvider = new \A2lix\TranslationFormBundle\Locale\DefaultProvider($locales, $defaultLocale, $requiredLocales);

        return new \A2lix\TranslationFormBundle\Form\Type\TranslationsType($translationsListener, $localProvider);
    }

    protected function getConfiguredTranslationsFormsType($locales, $defaultLocale, $requiredLocales)
    {
        $translationsFormsListener = new \A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener($this->getDefaultFormManipulator());
        $localProvider = new \A2lix\TranslationFormBundle\Locale\DefaultProvider($locales, $defaultLocale, $requiredLocales);

        return new \A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType($translationsFormsListener, $localProvider);
    }
}
