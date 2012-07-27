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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\Common\Collections\ArrayCollection;

class TranslationsType extends AbstractType
{
    private $em;
    private $annotationReader;
    private $translatableListener;

    public function __construct(EntityManager $em, FileCacheReader $annotationReader, TranslatableListener $translatableListener)
    {
        $this->em = $em;
        $this->annotationReader = $annotationReader;
        $this->translatableListener = $translatableListener;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dataClass = $builder->getParent()->getDataClass();
        $translatableConfig = $this->translatableListener->getConfiguration($this->em, $dataClass);

        $fields = array();
        foreach ($translatableConfig['fields'] as $field) {
            $annotations = $this->annotationReader->getPropertyAnnotations(new \ReflectionProperty($translatableConfig['useObjectClass'], $field));
            $mappingColumn = array_filter($annotations, function($item) {
                return $item instanceof \Doctrine\ORM\Mapping\Column;
            });
            $fields[$field] = ($mappingColumn[1]->type === 'string') ? 'text' : 'textarea';
        }

        foreach ($options['locales'] as $locale) {
            $builder->add($locale, 'translationsLocale', array(
                'fields' => $fields
            ));
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($builder) {
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            // Sort by locales and fields
            $dataLocale = array();
            foreach ($data as $d) {
                if (!isset($dataLocale[$d->getLocale()])) {
                    $dataLocale[$d->getLocale()] = new ArrayCollection();
                }
                $dataLocale[$d->getLocale()][$d->getField()] = $d;
            }

            foreach ($form->getChildren() as $translationsLocaleForm) {
                $locale = $translationsLocaleForm->getName();
                if (isset($dataLocale[$locale])) {
                    foreach ($translationsLocaleForm as $translationField) {
                        $field = $translationField->getName();
                        if (isset($dataLocale[$locale][$field])) {
                            $translationField->setData($dataLocale[$locale][$field]->getContent());
                        }
                    }
                }
            }
        });

        $builder->addEventListener(FormEvents::BIND, function(FormEvent $event) use ($builder, $translatableConfig) {
            $form = $event->getForm();
            $data = $event->getData();

            if (is_array($data)) {
                $data = new ArrayCollection();

            } else {
                // Remove new elements with wrong format
                foreach ($data as $key => $d) {
                    if (!is_numeric($key)) {
                        $data->removeElement($d);
                    }
                }
            }

            // Add/Update new elements with right format
            $newData = new ArrayCollection();
            foreach ($form->getChildren() as $translationsLocaleForm) {
                $locale = $translationsLocaleForm->getName();
                foreach ($translationsLocaleForm->getChildren() as $translation) {
                    $field = $translation->getName();
                    $content = $translation->getData();

                    $existingTranslationEntity = $data->filter(function($entity) use ($locale, $field) {
                        return ($entity->getLocale() === $locale && $entity->getField() === $field);
                    })->first();

                    if ($existingTranslationEntity) {
                        $existingTranslationEntity->setContent($content);
                        $newData->add($existingTranslationEntity);

                    } else {
                        $translationEntity = new $translatableConfig['translationClass'];
                        $translationEntity->setLocale($locale);
                        $translationEntity->setField($field);
                        $translationEntity->setContent($content);
                        $newData->add($translationEntity);
                    }
                }
            }

            $event->setData($newData);
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->setVar('locales', $options['locales']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'locales' => array()
        ));
    }

    public function getName()
    {
        return 'translations';
    }
}

