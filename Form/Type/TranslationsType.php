<?php

namespace A2lix\TranslationFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\FileCacheReader;
use Gedmo\Translatable\TranslatableListener;
use A2lix\TranslationFormBundle\EventListener\TranslationFormSubscriber;

/**
 * Regroup by locales, all translations fields
 *
 * @author David ALLIX
 */
class TranslationsType extends AbstractType
{
    private $em;
    private $annotationReader;
    private $translatableListener;
    private $locale;
    private $locales;

    public function __construct(EntityManager $em, FileCacheReader $annotationReader, TranslatableListener $translatableListener, $locale = 'en', array $locales = array())
    {
        $this->em = $em;
        $this->annotationReader = $annotationReader;
        $this->translatableListener = $translatableListener;
        $this->locale = $locale;
        $this->locales = $locales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Translatable Annotations
        $dataClass = $builder->getParent()->getDataClass();
        $translatableConfig = $this->translatableListener->getConfiguration($this->em, $dataClass);

        $fieldsConfig = array();
        foreach ($translatableConfig['fields'] as $fieldName) {
            $fieldConfig = array();
            
            // Custom field configuration
            if (isset($options['fields'][$fieldName])) {
                // Prevent display
                if (isset($options['fields'][$fieldName]['display']) && ($options['fields'][$fieldName]['display'] === false)) {
                    break;
                }
                
                // Custom Type
                if (isset($options['fields'][$fieldName]['type'])) {
                    $fieldConfig['type'] = $options['fields'][$fieldName]['type'];

                // Auto Type
                } else {                    
                    $fieldConfig['type'] = $this->detectFieldType($translatableConfig['useObjectClass'], $fieldName);
                }
                
                // Auto Label
                if (!isset($options['fields'][$fieldName]['label'])) {
                    $fieldConfig['label'] = ucfirst($fieldName);
                }
                
                // Other options from the field type (max_length, required, trim, read_only, ...)
                $fieldConfig += $options['fields'][$fieldName];
            
            // Auto field configuration
            } else {
                $fieldConfig = array(
                    'type' => $this->detectFieldType($translatableConfig['useObjectClass'], $fieldName),
                    'label' => ucfirst($fieldName)
                );
            }
            
            $fieldsConfig[$fieldName] = $fieldConfig;
        }

        
        foreach ($options['locales'] as $locale) {
            $builder->add($locale, 'translationsLocale', array(
                'fields' => $fieldsConfig
            ));
        }

        $subscriber = new TranslationFormSubscriber($builder->getFormFactory(), $translatableConfig['translationClass']);
        $builder->addEventSubscriber($subscriber);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->set('locale', (array) $options['locale']);
        $view->set('locales', $options['locales']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'by_reference' => false,
            'locale' => $this->locale,
            'locales' => $this->locales,
            'fields' => array()
        ));
    }

    public function getName()
    {
        return 'translations';
    }
    
    
    /**
     * Detect field type from Doctrine Annotations
     * 
     * @param string $class
     * @param string $field
     * 
     * @return string text|textarea
     */
    private function detectFieldType($class, $field)
    {
        $annotations = $this->annotationReader->getPropertyAnnotations(new \ReflectionProperty($class, $field));
        $mappingColumn = array_filter($annotations, function($item) {
            return $item instanceof \Doctrine\ORM\Mapping\Column;
        });
        $mappingColumnCurrent = current($mappingColumn);

        return ($mappingColumnCurrent->type === 'string') ? 'text' : 'textarea';
    }
}

