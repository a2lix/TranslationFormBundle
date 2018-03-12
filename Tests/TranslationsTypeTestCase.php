<?php

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests;

use A2lix\TranslationFormBundle\Form\EventListener\TranslationsFormsListener;
use A2lix\TranslationFormBundle\Form\EventListener\TranslationsListener;
use A2lix\TranslationFormBundle\Form\Type\TranslationsFieldsType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\Locale\DefaultProvider;
use A2lix\TranslationFormBundle\TranslationForm\TranslationForm;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Test\DoctrineTestHelper;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormRegistry;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;

abstract class TranslationsTypeTestCase extends TypeTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $emRegistry;

    protected function setUp()
    {
        $this->em = DoctrineTestHelper::createTestEntityManager();
        $this->emRegistry = $this->getEmRegistry($this->em);

        $schemaTool = new SchemaTool($this->em);

        $classes = [];
        foreach ($this->getUsedEntityFixtures() as $class) {
            $classes[] = $this->em->getClassMetadata($class);
        }

        try {
            $schemaTool->dropSchema($classes);
            $schemaTool->createSchema($classes);
        } catch (\Exception $e) {
        }

        parent::setUp();

        $formExtensions = [new DoctrineOrmExtension($this->emRegistry)];
        $resolvedFormTypeFactory = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $formRegistry = new FormRegistry($formExtensions, $resolvedFormTypeFactory);
        $translationForm = new TranslationForm($formRegistry, $this->emRegistry);
        $translationsListener = new TranslationsListener($translationForm);
        $translationsFormsListener = new TranslationsFormsListener();

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
            ->will($this->returnValue([]));

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
            ->addTypes([
                new TranslationsType($translationsListener, new DefaultProvider(['fr', 'en', 'de'], 'en')),
                new TranslationsFieldsType(),
                new TranslationsFormsType(
                    $translationForm,
                    $translationsFormsListener,
                    new DefaultProvider(['fr', 'en', 'de'], 'en')
                ),
            ])
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

    protected function getUsedEntityFixtures()
    {
        return [];
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

        return new Registry($container, [], ['default' => 'doctrine.orm.default_entity_manager'], 'default', 'default');
    }
}
