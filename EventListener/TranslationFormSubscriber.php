<?php

namespace A2lix\TranslationFormBundle\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Listener for preset and bind data for the translations type
 *
 * @author David ALLIX
 */
class TranslationFormSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $translationClass;

    public function __construct(FormFactoryInterface $factory, $translationClass)
    {
        $this->factory = $factory;
        $this->translationClass = $translationClass;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::BIND => 'bind',
        );
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

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
    }
    
    public function bind(DataEvent $event)
    {
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
                    return (($entity->getLocale() === $locale) && ($entity->getField() === $field));
                })->first();

                if ($existingTranslationEntity) {
                    $existingTranslationEntity->setContent($content);
                    $newData->add($existingTranslationEntity);

                } else {
                    $translationEntity = new $this->translationClass();
                    $translationEntity->setLocale($locale);
                    $translationEntity->setField($field);
                    $translationEntity->setContent($content);
                    $newData->add($translationEntity);
                }
            }
        }

        $event->setData($newData);
    }    
}