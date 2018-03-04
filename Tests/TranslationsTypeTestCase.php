<?php

namespace A2lix\TranslationFormBundle\Tests;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class TranslationsTypeTestCase extends TypeTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $emRegistry;

    protected function getUsedEntityFixtures()
    {
        return array();
    }

    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Form\Form')) {
            $this->markTestSkipped('The "Form" component is not available');
        }

        if (!class_exists('Doctrine\DBAL\Platforms\MySqlPlatform')) {
            $this->markTestSkipped('Doctrine DBAL is not available.');
        }

        if (!class_exists('Doctrine\Common\Version')) {
            $this->markTestSkipped('Doctrine Common is not available.');
        }

        if (!class_exists('Doctrine\ORM\EntityManager')) {
            $this->markTestSkipped('Doctrine ORM is not available.');
        }

        $this->em = DoctrineTestHelper::createTestEntityManager();
        $this->emRegistry = $this->getEmRegistry($this->em);

        $schemaTool = new SchemaTool($this->em);

        foreach ($this->getUsedEntityFixtures() as $class) {
            $classes[] = $this->em->getClassMetadata($class);
        }

        try {
            $schemaTool->dropSchema($classes);
        } catch (\Exception $e) {
        }

        try {
            $schemaTool->createSchema($classes);
        } catch (\Exception $e) {
        }


        parent::setUp();

        $formExtensions = array(new DoctrineOrmExtension($this->emRegistry));
        $resolvedFormTypeFactory = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $formRegistry = new FormRegistry(
            $formExtensions,
            $resolvedFormTypeFactory
        );

        $translationForm = new \A2lix\TranslationFormBundle\TranslationForm\TranslationForm($formRegistry, $this->emRegistry);
        $translationsListener = new \A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener($translationForm);
        $translationsFormsListener = new \A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener();

        if (interface_exists('Symfony\Component\Validator\Validator\ValidatorInterface')) {
            $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
                 ->disableOriginalConstructor()
                 ->getMock();
        } else {
            $validator = $this->getMockBuilder('Symfony\Component\Validator\ValidatorInterface')
                 ->disableOriginalConstructor()
                 ->getMock();
        }

        $validator->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(array()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions(
                $formExtensions
            )
            ->addTypeExtension(new FormTypeValidatorExtension($validator))
            ->addTypeGuesser(
                $this->getMockBuilder('Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser')
                     ->disableOriginalConstructor()
                     ->getMock()
            )
            ->addTypes(array(
                new \A2lix\TranslationFormBundle\Form\Type\TranslationsType(
                    $translationsListener,
                    new \A2lix\TranslationFormBundle\Locale\DefaultProvider(array('fr','en','de'), 'en')
                ),
                new \A2lix\TranslationFormBundle\Form\Type\TranslationsFieldsType(),
                new \A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType(
                    $translationForm,
                    $translationsFormsListener,
                    new \A2lix\TranslationFormBundle\Locale\DefaultProvider(array('fr','en','de'), 'en')
                ),
            ))
            ->getFormFactory();

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em = null;
        $this->emRegistry = null;
    }

    protected function persist(array $entities)
    {
        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }

        $this->em->flush();
        // no clear, because entities managed by the choice field must
        // be managed!
    }

    protected function getEmRegistry($em)
    {
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects($this->any())
                  ->method('get')
                  ->with($this->equalTo('doctrine.orm.default_entity_manager'))
                  ->will($this->returnValue($em));

        return new Registry($container, array(), array('default' => 'doctrine.orm.default_entity_manager'), 'default', 'default');
    }
}
