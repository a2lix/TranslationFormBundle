<?php

namespace A2lix\TranslationFormBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    A2lix\TranslationFormBundle\TranslationForm\TranslationForm;

/**
 *
 * @author David ALLIX
 */
class DefaultTranslationsSubscriber implements EventSubscriberInterface
{
    private $translationForm;

    /**
     *
     * @param \A2lix\TranslationFormBundle\TranslationForm\TranslationForm $translationForm
     */
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

    /**
     *
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();

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