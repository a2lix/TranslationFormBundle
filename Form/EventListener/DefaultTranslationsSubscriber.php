<?php

namespace A2lix\TranslationFormBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use A2lix\TranslationFormBundle\TranslationForm\TranslationForm;

class DefaultTranslationsSubscriber implements EventSubscriberInterface
{
    private $translationForm;

    public function __construct(TranslationForm $translationForm)
    {
        $this->translationForm = $translationForm;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    public function preSetData(FormEvent $event)
    {
        $this->createChildren($event->getForm());
    }

    /**
     *
     * @param type $form
     */
    private function createChildren($form)
    {
        $this->translationForm->init($form->getParent()->getConfig()->getDataClass());
        $translationClass = $this->translationForm->getTranslationClass();

        $formOptions = $form->getConfig()->getOptions();
        $childrenOptions = $this->translationForm->getChildrenOptions($formOptions);

        foreach ($formOptions['locales'] as $locale) {
            if (isset($childrenOptions[$locale])) {
                $form->add($locale, 'a2lix_translationsFields', array(
                    'data_class' => $translationClass,
                    'fields' => $childrenOptions[$locale]
                ));
            }
        }
    }
}