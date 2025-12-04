<?php declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Tests\Form;

use A2lix\AutoFormBundle\Form\Builder\AutoTypeBuilder;
use A2lix\AutoFormBundle\Form\Type\AutoType;
use A2lix\AutoFormBundle\Form\TypeGuesser\TypeInfoTypeGuesser;
use A2lix\TranslationFormBundle\Form\Extension\LocaleExtension;
use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use A2lix\TranslationFormBundle\LocaleProvider\SimpleLocaleProvider;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\Form\FormTypeGuesserChain;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\Traits\ValidatorExtensionTrait;
use Symfony\Component\Form\Test\TypeTestCase as BaseTypeTestCase;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

abstract class TypeTestCase extends BaseTypeTestCase
{
    use ValidatorExtensionTrait;

    protected $locales = ['en', 'fr', 'de'];
    protected $defaultLocale = 'en';
    protected $requiredLocales = ['en', 'fr'];

    private ?EntityManagerInterface $entityManager = null;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        VarDumper::setHandler(static function (mixed $var): void {
            /** @psalm-suppress PossiblyInvalidArgument */
            new HtmlDumper()->dump(
                new VarCloner()->cloneVar($var),
                @fopen(__DIR__.'/../../dump.html', 'a')
            );
        });
    }

    #[\Override]
    protected function getExtensions(): array
    {
        $autoType = new AutoType(
            new AutoTypeBuilder($this->getPropertyInfoExtractor()),
            ['id']
        );

        $localeProvider = new SimpleLocaleProvider($this->locales, $this->defaultLocale, $this->requiredLocales);
        $localeExtension = new LocaleExtension($localeProvider);
        $translationsType = new TranslationsType();
        $translationsFormsType = new TranslationsFormsType();

        return [
            ...parent::getExtensions(),
            new PreloadedExtension([$autoType, $translationsType, $translationsFormsType], [$localeExtension], $this->getFormTypeGuesserChain()),
        ];
    }

    private function getEntityManager(): EntityManagerInterface
    {
        if (null !== $this->entityManager) {
            return $this->entityManager;
        }

        $configuration = ORMSetup::createAttributeMetadataConfig([__DIR__.'/../Fixtures/Entity'], true);
        $configuration->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $configuration);

        return $this->entityManager = new EntityManager($connection, $configuration);
    }

    private function getPropertyInfoExtractor(): PropertyInfoExtractor
    {
        $doctrineExtractor = new DoctrineExtractor($this->getEntityManager());
        $reflectionExtractor = new ReflectionExtractor();

        return new PropertyInfoExtractor(
            listExtractors: [
                $reflectionExtractor,
                $doctrineExtractor,
            ],
            typeExtractors: [
                $doctrineExtractor,
                new PhpStanExtractor(),
                new PhpDocExtractor(),
                $reflectionExtractor,
            ],
            accessExtractors: [
                $doctrineExtractor,
                $reflectionExtractor,
            ]
        );
    }

    private function getFormTypeGuesserChain(): FormTypeGuesserChain
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($this->getEntityManager());

        return new FormTypeGuesserChain([
            new DoctrineOrmTypeGuesser($managerRegistry),
            new TypeInfoTypeGuesser(TypeResolver::create()),
        ]);
    }
}
